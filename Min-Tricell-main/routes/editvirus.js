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
const pug_editvirus = pug.compileFile('./masterframe/editvirus.html');
const { getVirusImagesHTML } = require('./virusimages.js');

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
var htmlVirusimagesCSS = readHTML('./masterframe/virusimages_css.html');

// ---------------------- Ladda upp PDF ------------------------------------------------
/*router.post('/uploadpdf/:virusnumber', function(request, response)
{
    var virusnumber = request.params.virusnumber;
    var form = new formidable.IncomingForm();
    form.parse(request, function(err, fields, files)
    {
        if(files.pdffile && files.pdffile.originalFilename != "")
        {
            var oldpath = files.pdffile.filepath;
            var newpath = path.resolve(__dirname, "../public/pdf/" + virusnumber + ".pdf");
            fs.renameSync(oldpath, newpath);
            response.send("PDF uploaded successfully");
        }
        else
        {
            response.send("No file selected");
        }
    });
});

// ---------------------- Ta bort PDF ------------------------------------------------
router.get('/deletepdf/:virusnumber', function(request, response)
{
    var virusnumber = request.params.virusnumber;
    var pdfpath = path.resolve(__dirname, "../public/pdf/" + virusnumber + ".pdf");
    if(fs.existsSync(pdfpath))
    {
        fs.unlinkSync(pdfpath);
        response.send("PDF deleted");
    }
    else
    {
        response.send("PDF not found");
    }
});*/


// ---------------------- Editera virus ------------------------------------------------
router.post('/:id', function(request, response)
{
    //Ta emot variablerna från formuläret
    if(request.session.loggedin && (request.session.securityAccessLevel == "B" || request.session.securityAccessLevel == "A"))
    {
        var form = new formidable.IncomingForm();
        form.parse(request, function (err, fields, files) 
        {
            var id = request.params.id;
            var virusnumber = fields.fvirusnumber;
            //var virusname = fields.fvirusname;
           // var createdate = fields.fcreatedate;
           // var creator = fields.fcreator;
            var description = fields.fdescription;
            var virusvideo = fields.fvirusvideo;
            var virushandlingvideo = fields.fvirushandlingvideo;

            //Öppna databasen
            const ADODB = require('node-adodb');
            const connection = ADODB.open('Provider=Microsoft.Jet.OLEDB.4.0;Data Source=./data/mdb/researchdata.mdb;');

            async function sqlQuery() {
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
                response.write(htmlVirusimagesCSS);
                response.write(htmlInfoStart);

                //Skriv in i databasen
                const result = await connection.execute("UPDATE ResearchObjects SET objectText='"+description.replace(/'/g, "''")+"', presentationVideoLink='"+virusvideo.replace(/'/g, "''")+"', securityVideoLink='"+virushandlingvideo.replace(/'/g, "''")+"' WHERE id="+id+"");

                //Ladda upp filen om det finns en fil
                var ffile = Array.isArray(files.ffile) ? files.ffile[0] : files.ffile;
                if(ffile && ffile.originalFilename != ""){
                    var oldpath = ffile.filepath;
                    var newpath = path.resolve(__dirname, "../public/pdf/"+ virusnumber +".pdf");
                    try {
                        fs.renameSync(oldpath, newpath);
                    } catch(e) {
                        fs.copyFileSync(oldpath, newpath);
                        fs.unlinkSync(oldpath);
                    }
                }
                //Ge respons till användaren
                response.write('<p style="font-size:18px; color:green;">Virus edited</p>');
                response.write('<a href="http://localhost:3000/api/virusdatabase/" style="display:inline-block; margin-top:20px; padding:10px 20px; background-color:#007BFF; color:#fff; text-decoration:none; border-radius:5px;">Edit another virus</a>');

                response.write(htmlInfoStop);
                response.write(htmlFooter);
                response.write(htmlBottom);
                response.end();

            }
            sqlQuery();
        });
    }
    else{
        response.setHeader('Content-type','text/html');
        response.write(htmlHead);
        response.write(htmlHeader);
        response.write(htmlMenu);
        response.write(htmlInfoStart);
        response.write('<p style="font-size:18px; color:red;">Security access level too low</p>');
        response.write('<a href="http://localhost:3000/api/virusdatabase/" style="display:inline-block; margin-top:20px; padding:10px 20px; background-color:#007BFF; color:#fff; text-decoration:none; border-radius:5px;">Go back</a>');
        response.write(htmlInfoStop);
        response.write(htmlFooter);
        response.write(htmlBottom);
        response.end();
    }

});

router.get('/:id', function(request, response)
{
    var id = request.params.id;
    //Öppna databasen
    const ADODB = require('node-adodb');
    const connection = ADODB.open('Provider=Microsoft.Jet.OLEDB.4.0;Data Source=./data/mdb/researchdata.mdb;');

    async function sqlQuery() {
        //Läs nuvarande värden ur databasen 
        const result = await connection.query("SELECT * FROM ResearchObjects WHERE id="+id+"");
        let str_objectNumber = "" + result[0]['objectNumber'];
        let str_objectName = "" + result[0]['objectName'];
        let str_objectCreator = "" + result[0]['objectCreator'];
        let str_objectCreatedDate = "" + result[0]['objectCreatedDate'];
        let str_objectText = "" + result[0]['objectText'];
        let str_presentationVideoLink = "" + result[0]['presentationVideoLink'];
        let str_securityVideoLink = "" + result[0]['securityVideoLink'];

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
            // Kollar om personen har odf fil
            const path = "./public/pdf/"+str_objectNumber+".pdf";
            if(fs.existsSync(path))
            {
                pdf = str_objectNumber+".pdf";
            }
            else
            {
                pdf = "";
            }

            htmlNewVirusCSS = readHTML('./masterframe/newvirus_css.html');
            response.write(htmlNewVirusCSS);
            htmlNewVirusJS = readHTML('./masterframe/newvirus_js.html');
            response.write(htmlNewVirusJS);
            response.write(htmlVirusimagesCSS);
            response.write(pug_editvirus({
                id: id,
                virusnumber: str_objectNumber,
                virusname: str_objectName,
                createdate: str_objectCreatedDate,
                creator: str_objectCreator,
                description: str_objectText,
                viruspdf: pdf,
                virusvideo: str_presentationVideoLink,
                virushandlingvideo: str_securityVideoLink,
            }));
            response.write(getVirusImagesHTML(id));
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