# Kid-A Data Importer

Starting with an Export from Mr. Data Converter, we copy and paste the album data from our Google Doc. We export to a PHP array, and then paste into `albumData.php`. From there, we need to kick off a PHP server, to generate the file. This can be done on a Mac by typing this into a terminal window in the working directory of the project.

```sh
$ php -S localhost:8000
```

With the server running, we can visit the following URL http://localhost:8000/app.php to download the CSV, to use in InDesign.

