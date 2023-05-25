<?php

/*
The code is a PHP class called `PDFMerger` that allows you to merge multiple PDF files into a single PDF file using the TCPDI library. The class has three methods:
1. `__construct()`: 
   This is the constructor function for the class, which sets up the environment for the class by finding the path to the `composer` directory and including the necessary files.
2. `addPDF($filepath, $pages = 'all')`: 
   This method adds a PDF file to an array of files with the option to specify which pages to add.
3. `merge($outputmode = 'browser', $outputname = 'newfile.pdf')`: 
   This method merges multiple PDF files into a single PDF file using the TCPDI library. It takes two optional parameters: 
   `$outputmode` specifies the mode in which the merged PDF should be outputted, and `$outputPath` specifies the file path for the merged PDF.

The class also has two private properties:
1. `$_files`: 
   This is an array that holds the PDF files and pages to be merged.
2. `$_fpdi`: 
   This is an instance of the TCPDI class, which is used to merge the PDF files.

Overall, this class provides a simple way to merge multiple PDF files into a single PDF file using the TCPDI library.
*/

namespace PDFMerger;

class PDFMerger {
    private $_files = [];
    private $_fpdi;


    /*
    This is a constructor function for a class, which means that it will be automatically called when an object of the class is created. The purpose of this function is to set up the environment for the rest of the class to function properly. 
    The first line gets the absolute path to the parent directory of the current working directory using the `getcwd()` function and then appending `'/../'`. This is likely done to go up one level in the directory structure to find the root directory of the project.
    The `scandir()` function is then used to scan the root directory and return an array of all the files and directories it contains. The `foreach` loop then iterates through each element of this array, checking if the element contains the substring `'composer'` and is also a directory using the `strpos()` and `is_dir()` functions respectively.
    If the condition is true, then the path to the `composer` directory is constructed using the `$parentDirectory` variable and the `break` statement is used to exit the loop, as there should only be one `composer` directory.
    The last two lines of the function then include the `tcpdf.php` and `tcpdi.php` files from the `mergepdfs/tcpdf` directory within the `composer` directory. These files are likely required for some functionality within the class. 
    Overall, this constructor function sets up the environment for the class by finding the path to the `composer` directory and including the necessary files.
    */
    public function __construct() {
        $parentDirectory = realpath(getcwd() . '/../');

        foreach (scandir($parentDirectory) as $directory) {
            if (strpos($directory, 'composer') !== false && is_dir("$parentDirectory/$directory")) {
                $pathToComposer = "$parentDirectory/$directory";
                break;
            }
        }

        require_once "$pathToComposer/mergepdfs/tcpdf/tcpdf.php";
        require_once "$pathToComposer/mergepdfs/tcpdf/tcpdi.php";
    }


    /*
    This is a method in a PHP class that adds a PDF file to an array of files. Here is a breakdown of what the code does:
    - The method is called "addPDF" and takes two parameters: the filepath of the PDF file to add, and a string representing which pages of the PDF to add. The default value for the "pages" parameter is "all".
    - The first line of the method checks if the file exists at the given filepath using the "file_exists" function. If the file does not exist, an exception is thrown with an error message.
    - The second line of the method converts the "pages" parameter to lowercase and checks if it is equal to the string "all". If it is, the value of the "pages" variable is set to "all". If not, the "_rewritePages" method is called to process the string and return a new string representing the pages to add.
    - The third line of the method adds an array containing the filepath and pages to the "_files" array property of the class.
    Overall, this method is used to add a PDF file to an array of files with the option to specify which pages to add. If the specified file does not exist, an exception is thrown.
    */

    public function addPDF($filepath, $pages = 'all') {
        if (file_exists($filepath)) {
            $pages = strtolower($pages) === 'all' ? 'all' : $this->_rewritePages($pages);
            $this->_files[] = [$filepath, $pages];
        } else {
            throw new \Exception("Could not locate PDF on '$filepath'");
        }
    }

    /**
     * Combines the provided PDF files and saves the output to the specified location.
     *
     * @param string $outputmode The output mode to be used.
     * @param string $outputname The name of the output file.
     * @return PDF Returns the PDF object.
     * Further detail below:
     * This is a PHP function called "merge" that merges multiple PDF files into a single PDF file using the TCPDI library.
     * The function takes two optional parameters: $outputMode and $outputPath. $outputMode specifies the mode in which the merged PDF should be outputted, and $outputPath specifies the file path for the merged PDF. If $outputMode is not specified, the default value is 'browser'. If $outputPath is not specified, the default value is 'newfile.pdf'.
     * The function first checks if there are any PDF files to merge by checking if the private variable $_files is empty. If it is empty, the function throws an exception with the message "No PDFs to merge."
     * Next, the function creates a new instance of the TCPDI class and sets the print header and footer to false.
     * Then, the function iterates through each PDF file in $_files. Each file is represented as an array with two elements: $filename and $filePages. $filename is the path to the PDF file, and $filePages is an array of pages to include in the merged PDF, or the string 'all' to include all pages. If $filePages is 'all', the function adds all pages in the PDF to the merged PDF using the TCPDI class's setSourceFile(), importPage(), addPage(), and useTemplate() methods. If $filePages is an array of pages, the function adds each specified page to the merged PDF using the same methods.
     * After all PDF files have been merged, the function uses the _switchMode() private method to determine the output mode based on the value of $outputMode. If $outputMode is 'S', the function returns the merged PDF as a string using the TCPDI class's output() method. If $outputMode is 'F', the function saves the merged PDF to the file path specified in $outputPath and returns true. If $outputMode is anything else, the function saves the merged PDF to the file path specified in $outputPath using the specified output mode and returns true if successful. If the output is not successful, the function throws an exception with the message "Error outputting PDF to '$outputMode'."
     */

    public function merge($outputMode = 'browser', $outputPath = 'newfile.pdf') {
        if (empty($this->_files)) {
            throw new \Exception("No PDFs to merge.");
        }

        $fpdi = new \TCPDI;
        $fpdi->setPrintHeader(false);
        $fpdi->setPrintFooter(false);

        foreach ($this->_files as [$filename, $filePages]) {
            $count = $fpdi->setSourceFile($filename);

            if ($filePages === 'all') {
                for ($i = 1; $i <= $count; $i++) {
                    $template = $fpdi->importPage($i);
                    $size = $fpdi->getTemplateSize($template);
                    $orientation = $size['h'] > $size['w'] ? 'P' : 'L';
                    $fpdi->addPage($orientation, [$size['w'], $size['h']]);
                    $fpdi->useTemplate($template);
                }
            } else {
                foreach ($filePages as $page) {
                    if (!$template = $fpdi->importPage($page)) {
                        throw new \Exception("Could not load page '$page' in PDF '$filename'. Check that the page exists.");
                    }
                    $size = $fpdi->getTemplateSize($template);
                    $orientation = $size['h'] > $size['w'] ? 'P' : 'L';
                    $fpdi->addPage($orientation, [$size['w'], $size['h']]);
                    $fpdi->useTemplate($template);
                }
            }
        }

        $mode = $this->_switchMode($outputMode);

        if ($mode === 'S') {
            return $fpdi->output($outputPath, 'S');
        } elseif ($mode === 'F') {
            $fpdi->output($outputPath, $mode);
            return true;
        } else {
            if ($fpdi->output($outputPath, $mode) === '') {
                return true;
            } else {
                throw new \Exception("Error outputting PDF to '$outputMode'.");
            }
        }
    }

    /**
     * The output location in FPDI is specified using single characters. This function changes a more descriptive string into the correct format.
     *
     * @param $mode
     * @return string A single character representing the output location.
     * Further detail below:
     * This is a private PHP function called "_switchMode" that takes a single parameter "$mode". It is used to determine the type of output mode for a file or data stream.
     * The function uses a "switch" statement to check the value of the parameter after converting it to lowercase using the "strtolower" function.
     * If the value of the parameter matches one of the following cases: 'download', 'browser', 'file', or 'string', the corresponding letter 'D', 'I', 'F', or 'S' is returned, respectively.
     * If the value of the parameter does not match any of these cases, the default case is executed, which returns 'I', indicating that the output mode is set to "browser" by default.
     * In summary, this function provides a convenient way to map human-readable output mode names to their corresponding single-letter codes for use in generating file or data stream output.
     */
    private function _switchMode($mode) {
        switch (strtolower($mode)) {
            case 'download':
                return 'D';
            case 'browser':
                return 'I';
            case 'file':
                return 'F';
            case 'string':
                return 'S';
            default:
                return 'I';
        }
    }


    /**
     * This function accepts a string of page numbers in the format of "1,3,4,16-50" and converts it into an array of individual page numbers.
     *
     * @param string $pages A comma-separated list of page numbers with optional range values separated by a hyphen.
     * @return array An array of individual page numbers.
     * Further detail below:
     * Added initialization of $newpages array before use to avoid undefined variable warning.
     * Removed unnecessary comments.
     * Changed while loop to a for loop for better performance.
     * Added type casting to ensure that variables are integers.
     * Moved the $_SESSION assignment outside of the foreach loop to avoid overwriting it repeatedly.
     */
    private function _rewritepages($pages) {
        $newpages = array();
        $pages = str_replace(' ', '', $pages);
        $part = explode(',', $pages);
        foreach ($part as $i) {
            $ind = explode('-', $i);
            if (count($ind) == 2) {
                $x = (int)$ind[0];
                $y = (int)$ind[1];
                if ($x > $y) {
                    throw new Exception("Starting page, '$x' is greater than ending page '$y'.");
                }
                for ($j = $x; $j <= $y; $j++) {
                    $newpages[] = $j;
                }
            } else {
                $newpages[] = (int)$ind[0];
            }
        }
        $_SESSION["PAGES"] = $newpages;
        return $newpages;
    }
}    
