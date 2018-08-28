INSTALLATION INSTRUCTIONS
--------------------------------------------------------------------------------------

- Make a local copy of the source code repository somewhere in your local machine:<br/>
<code>git clone https://gitlab.com/nexus-platform/backend-sf4-api</code>

- Install PHP dependencies using composer.<br/>
<code>cd backend-sf4-api
composer install
composer update</code>
- Edit .env file and update DB connection string (DATABASE_URL=mysql://user:pass@server:port/database).<br/>
If the database does not exist yet, create it manually or execute <code>php bin/console doctrine:database:create</code> to generate it.

- Generate migrations and apply them to update the database.<br/>
<code>php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate</code>

- Populate app_settings table with the parameters from the application's mail server.<br/>

- Install the latest version of PDFtk Server<br/>
Follow instructions <a href="https://www.pdflabs.com/tools/pdftk-server/" target="_blank">here</a> depending on your OS.
