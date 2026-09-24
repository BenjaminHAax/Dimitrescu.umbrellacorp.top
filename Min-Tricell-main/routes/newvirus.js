const express = require('express');
const router = express.Router();
const bodyParser = require('body-parser');
var formidable = require('formidable');

router.use(bodyParser.json());
router.use(bodyParser.urlencoded({ extended: false }));

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

// ---------------------- Lägg till ny person ------------------------------------------------
router.post('/', function(request, response)
{

    //Hitta creatorId för den inloggade användaren
    const creatorId = request.cookies.employeecode;

    //Hitta datum som dd.mm.yyyy format
    const date = new Date();
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    const currentDate = day + '.' + month + '.' + year;

    //Hitta tid som hh:mm format
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const currentTime = hours + ':' + minutes;

    //Ta emot variablerna från formuläret
    var form = new formidable.IncomingForm();
    form.parse(request, function (err, fields, files) 
    {
        var objectNumber = fields.fnumber;
        var objectName = fields.fname;
        var objectCreator = creatorId;
        var objectCreatedDate = currentDate;
        var objectCreatedTime = currentTime;
        var objectText = fields.ftext;
        var objectStatus = "open"; //Default och ska användas för senara versioner
        var presentationVideoLink = fields.fpresentationvideo;
        var securityVideoLink = fields.fhandlingvideo;

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
                //Lägg till viruset i databasen
                const insertResult = await connection.execute("INSERT INTO ResearchObjects (objectNumber, objectName, objectCreator, objectCreatedDate, objectCreatedTime, objectText, objectStatus, presentationVideoLink, securityVideoLink) VALUES ('"+objectNumber+"', '"+objectName+"', '"+objectCreator+"', '"+objectCreatedDate+"', '"+objectCreatedTime+"', '"+objectText+"', '"+objectStatus+"', '"+presentationVideoLink+"', '"+securityVideoLink+"')");

                //Ladda upp pdf:en
                if(files.ffile.originalFilename != ""){
                    var oldpath = files.ffile.filepath;
                    var newpath = "./public/pdf/"+objectNumber+".pdf";
                    fs.rename(oldpath, newpath, function (err) {
                        if (err) throw err;
                    });
                }

                //Ge respons till användaren
                response.write('<p style="font-size:18px; color:green;">Virus added successfully!</p>');
                response.write('<a href="http://localhost:3000/api/newvirus" style="display:inline-block; margin-top:20px; padding:10px 20px; background-color:#007BFF; color:#fff; text-decoration:none; border-radius:5px;">Add another virus</a>');
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

});

// ---------------------- Formulär för att lägga till ny person ------------------------------
router.get('/', (request, response) =>
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

    // Läs in formuläret
    if(request.session.loggedin && (request.session.securityAccessLevel == "B" || request.session.securityAccessLevel == "A"))
    {
        htmlNewVirusCSS = readHTML('./masterframe/newvirus_css.html');
        response.write(htmlNewVirusCSS);
        htmlNewVirusJS = readHTML('./masterframe/newvirus_js.html');
        response.write(htmlNewVirusJS);
        htmlNewVirus = readHTML('./masterframe/newvirus.html');
        response.write(htmlNewVirus);
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
});

module.exports = router;
