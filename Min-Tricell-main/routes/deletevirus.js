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

    var htmlHead = readHTML('./masterframe/head.html');
    var htmlHeader = readHTML('./masterframe/header.html');
    var htmlMenu = readHTML('./masterframe/menu.html');    
    var htmlInfoStart = readHTML('./masterframe/infoStart.html');
    var htmlInfoStop = readHTML('./masterframe/infoStop.html');
    var htmlFooter = readHTML('./masterframe/footer.html');
    var htmlBottom = readHTML('./masterframe/bottom.html');

    var htmlLoggedinMenuCSS = readHTML('./masterframe/loggedinmenu_css.html');
    var htmlLoggedinMenuJS = readHTML('./masterframe/loggedinmenu_js.html');
    var htmlLoggedinMenu = readHTML('./masterframe/loggedinmenu.html');

// ---------------------- Radera virus ------------------------------------------------
router.get('/:id', function(request, response)
{
    const id = parseInt(request.params.id);

    // Öppna databasen
    const ADODB = require('node-adodb');
    const connection = ADODB.open('Provider=Microsoft.Jet.OLEDB.4.0;Data Source=./data/mdb/researchdata.mdb;');

    async function sqlQuery()
    {
        response.setHeader('Content-type','text/html');
        response.write(htmlHead);
        if(request.session.loggedin)
        {
            response.write(htmlLoggedinMenuCSS);
            response.write(htmlLoggedinMenuJS);
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

        if(request.session.loggedin && (request.session.securityAccessLevel == "B" || request.session.securityAccessLevel == "A"))
        {

            //Ta reda på virusest objektNumber (för att kunna radera pdf)
            const result = await connection.query("SELECT objectNumber FROM ResearchObjects WHERE id="+id+"");
            let objectNumber = "" + result[0]['objectNumber'];

            //Radera viruset från databasen
            const deleteResult = await connection.execute("DELETE FROM ResearchObjects WHERE id="+id+"");

            //Radera virus entries från databasen
            const deleteEntriesResult = await connection.execute("DELETE FROM ResearchEntries WHERE researchObjectId='"+id+"'");

            //Radera pdf:en
            var path = "./public/pdf/"+objectNumber+".pdf";
            if(fs.existsSync(path))
            {
                fs.unlinkSync(path);
            }

            //Radera bilderna
            var imagePath = `./public/virusphoto/${id}`;
            if(fs.existsSync(imagePath))
            {
                fs.rmdirSync(imagePath, { recursive: true });
            }

            //Ge respons till användaren
            response.write('<p style="font-size:18px; color:green;">Virus deleted</p>');
            response.write('<a href="http://localhost:3000/api/virusdatabase/" style="display:inline-block; margin-top:20px; padding:10px 20px; background-color:#007BFF; color:#fff; text-decoration:none; border-radius:5px;">Delete another virus</a>');
        }
        else
        {
            response.write('<p style="font-size:18px; color:red;">Security access level too low</p>');
            response.write('<a href="http://localhost:3000/api/virusdatabase/" style="display:inline-block; margin-top:20px; padding:10px 20px; background-color:#007BFF; color:#fff; text-decoration:none; border-radius:5px;">Go back</a>');
        }

        response.write(htmlInfoStop);
        response.write(htmlFooter);
        response.write(htmlBottom);
        response.end();
    }
    sqlQuery();
});

//Special router för att radera endast PDF-filen, används i editvirus.js
router.get('/deletepdf/:id', function(request, response)
{
    const objectNumber = request.params.id;

    async function sqlQuery()
    {
        response.setHeader('Content-type','text/html');
        response.write(htmlHead);
        if(request.session.loggedin)
        {
            response.write(htmlLoggedinMenuCSS);
            response.write(htmlLoggedinMenuJS);
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

        if(request.session.loggedin && (request.session.securityAccessLevel == "B" || request.session.securityAccessLevel == "A"))
        {

            //Radera pdf:en
            var pdfpath = "./public/pdf/"+objectNumber+".pdf";
            if(fs.existsSync(pdfpath))
            {
                fs.unlinkSync(pdfpath);
                response.write('<p style="font-size:18px; color:green;">PDF deleted</p>');
                response.write('<a href="http://localhost:3000/api/virusdatabase/" style="display:inline-block; margin-top:20px; padding:10px 20px; background-color:#007BFF; color:#fff; text-decoration:none; border-radius:5px;">Edit virus again</a>');
            }
            else
            {
                response.write('<p style="font-size:18px; color:red;">PDF not found</p>');
                response.write('<a href="http://localhost:3000/api/virusdatabase/" style="display:inline-block; margin-top:20px; padding:10px 20px; background-color:#007BFF; color:#fff; text-decoration:none; border-radius:5px;">Go back</a>');
            }
        }
        else
        {
            response.write('<p style="font-size:18px; color:red;">Security access level too low</p>');
            response.write('<a href="http://localhost:3000/api/virusdatabase/" style="display:inline-block; margin-top:20px; padding:10px 20px; background-color:#007BFF; color:#fff; text-decoration:none; border-radius:5px;">Go back</a>');
        }
        response.write(htmlInfoStop);
        response.write(htmlFooter);
        response.write(htmlBottom);
        response.end();
    }
    sqlQuery();
});

module.exports = router;