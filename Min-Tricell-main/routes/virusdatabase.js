const express = require('express');
const router = express.Router();

router.use(express.static('./public'));
const path = require('path');

const pug = require('pug');
const { response } = require('express');
const pug_loggedinmenu = pug.compileFile('./masterframe/loggedinmenu.html');

// --------------------- Läs in Masterframen --------------------------------
const readHTML = require('../readHTML.js');
const fs = require('fs');
const { json } = require('express');

var htmlHead = readHTML('./masterframe/head.html');
var htmlHeader = readHTML('./masterframe/header.html');
var htmlMenu = readHTML('./masterframe/menu.html');    
var htmlInfoStart = readHTML('./masterframe/infoStart.html');
var htmlInfoStop = readHTML('./masterframe/infoStop.html');
var htmlFooter = readHTML('./masterframe/footer.html');
var htmlBottom = readHTML('./masterframe/bottom.html');

// ---------------------- Lista all virus, Metod 4: Databas -------------------------------
router.get('/', (request, response) =>
{
    //Öppna databasen
    const ADODB = require('node-adodb');
    const dbPath = path.join(__dirname, '../data/mdb/researchdata.mdb');
    const connection = ADODB.open(`Provider=Microsoft.Jet.OLEDB.4.0;Data Source=${dbPath};`);

    async function sqlQuery() 
    {
        response.writeHead(200, {'Content-Type': 'text/html;'});
        response.write(htmlHead);
        if(request.session.loggedin)
            {
            htmlLoggedinMenuCSS = readHTML('./masterframe/loggedinmenu_css.html');
            response.write(htmlLoggedinMenuCSS);
            htmlLoggedinMenuJS = readHTML('./masterframe/loggedinmenu_js.html');
            response.write(htmlLoggedinMenuJS);
            //htmlLoggedinMenu = readHTML('./masterframe/loggedinmenu.html');
            //response.write(htmlLoggedinMenu);
            response.write(pug_loggedinmenu({
                employeecode: request.cookies.employeecode,
                name: request.cookies.name,
                logintimes: request.cookies.logintimes,
                lastlogin: request.cookies.lastlogin,
            }));
        }
        response.write(htmlHeader);
        response.write(htmlMenu);
        response.write(htmlInfoStart);

        //Skapa HTML-textsträng för tabellen för utskrift av XML-data
        let htmlOutput = "" +
        "<link rel='stylesheet' href='/css/virusdatabase.css'>" 

        if(request.session.loggedin && (request.session.securityAccessLevel == "B" || request.session.securityAccessLevel == "A")) 
        {
            htmlOutput += "<table border='0'>";
            htmlOutput +="<tr><td width=\"350\" align=\"left\">";
            htmlOutput +="<h2>Virus Database:</h2>\n";
            htmlOutput +="</td><td width=\"350\" align=\"right\">";
            htmlOutput +="<a href=\"http://localhost:3000/api/newvirus\" style=\"color:#336699;text-decoration:none;\">Add new virus</a>";
            htmlOutput +="</td></tr></table>";
        }
        else
        {
            htmlOutput += "<h2>Virus Database:</h2>\n";
        }

        htmlOutput +="<div id=\"table-virus\">"+
        "<div id=\"table-header\">\n"+
        "<div class=\"table-header-cell-right\">Number</div>\n"+
        "<div class=\"table-header-cell-left\">Name</div>\n"+
        "<div class=\"table-header-cell-left\">Created</div>\n"+
        "<div class=\"table-header-cell-left\">By</div>\n"+
        "<div class=\"table-header-cell-middle\">Entries</div>\n"+
        "<div class=\"table-header-cell-center\">Last Entry</div>\n";
        if(request.session.loggedin && (request.session.securityAccessLevel == "B" || request.session.securityAccessLevel == "A"))
        {
            htmlOutput +="<div class=\"table-header-cell-edit\">Edit</div>\n"+
            "<div class=\"table-header-cell-delete\">Delete</div>\n";
        }
        htmlOutput +="</div>\n\n"+
        "<div id=\"table-body\">\n";
        "";

        // Skicka SQL-query till databasen och läs in variabler
        const result = await connection.query('SELECT [ID],[objectNumber], [objectName], [objectCreator], [objectCreatedDate] FROM [ResearchObjects]');

        //Hitta antalet enries och senaste entry för varje virus från tabellen ResearchEntries
        const resultEntries = await connection.query('SELECT * FROM [ResearchEntries]');
        
            
        // Ta reda på antalet employees
        var count =  result.length;
        const countEntries = resultEntries.length;

        //Loopa genom och skriv ut alla virus i tabellen
        let i,j;
        var entries = 0;
        var lastEntry = "-";
        for(i = 0; i < count; i++)
        {
            var id = result[i].ID;
            var objectNumber = result[i].objectNumber;
            var objectName = result[i].objectName;
            var objectCreator = result[i].objectCreator;
            var objectCreatedDate = result[i].objectCreatedDate;

            entries = 0;
            lastEntry = "-";

            //Hitta antalet entries och senaste entry för respektive virus genom att loopa genom alla entries och jämföra id med researchObjectId i ResearchEntries
            //Lite stöd av AI
            for(j = 0; j < countEntries; j++){
                if(resultEntries[j].researchObjectId == id){
                    entries++;
                    var rawDate = resultEntries[j].entryDate;
                    if(rawDate){
                        var entryDate;
                        if(rawDate instanceof Date){
                            // node-adodb returnerade ett Date-objekt, formatera till DD.MM.YYYY
                            entryDate = rawDate.getDate().toString().padStart(2,'0') + '.' +
                                        (rawDate.getMonth()+1).toString().padStart(2,'0') + '.' +
                                        rawDate.getFullYear();
                        } else {
                            entryDate = String(rawDate);
                        }
                        if(lastEntry === "-"){
                            lastEntry = entryDate;
                        } else {
                            var eParts = entryDate.split('.');
                            var lParts = lastEntry.split('.');
                            var parsedEntry = new Date(eParts[2], eParts[1]-1, eParts[0]);
                            var parsedLast  = new Date(lParts[2], lParts[1]-1, lParts[0]);
                            if(parsedEntry > parsedLast){
                                lastEntry = entryDate;
                            }
                        }
                    }
                }
            }

            //Lägg till respektive virus till utskrift-variabeln
            htmlOutput += "<div class='resp-table-row'>\n";
            htmlOutput += "<div class='table-body-cell-right'>" + objectNumber + "</div>\n";
            htmlOutput += "<div class='table-body-cell-left'>" + objectName + "</div>\n";
            htmlOutput += "<div class='table-body-cell-left'>" + objectCreatedDate + "</div>\n";
            htmlOutput += "<div class='table-body-cell-left'>" + objectCreator + "</div>\n";
            htmlOutput += "<div class='table-body-cell-middle'>" + entries + "</div>\n";
            htmlOutput += "<div class='table-body-cell-center'>" + lastEntry + "</div>\n";

            if(request.session.loggedin && (request.session.securityAccessLevel == "B" || request.session.securityAccessLevel == "A")) {
                htmlOutput += "<div class='table-body-cell-edit'><a href=\"http://localhost:3000/api/editvirus/" + id + "\">&#9998;</a></div>\n";
                htmlOutput += "<div class='table-body-cell-delete'><a href=\"http://localhost:3000/api/deletevirus/" + id + "\">&#x2715;</a></div>\n";
            }
            htmlOutput += "</div>\n";
        }
        htmlOutput += "</div></div>\n\n";
        response.write(htmlOutput); // Skriv ut XML-data
        response.write(htmlInfoStop);
        response.write(htmlFooter);
        response.write(htmlBottom);
        response.end();
    }
    sqlQuery();
});

module.exports = router;