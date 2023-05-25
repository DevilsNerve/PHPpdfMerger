# PDFMerger

The `PDFMerger` class is a PHP class that allows you to merge multiple PDF files into a single PDF file using the TCPDI library.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Methods](#methods)
- [Examples](#examples)
- [License](#license)

## Installation

To use the `PDFMerger` class, follow these steps:

1. Ensure that you have PHP installed on your system.
2. Download the TCPDI library from the [TCPDI GitHub repository](https://github.com/pauln/tcpdi) and include it in your project.
3. Copy the `PDFMerger` class code into your project or include the class file.

## Usage

To start using the `PDFMerger` class, you need to create an instance of the class and then add the PDF files you want to merge using the `addPDF` method. Finally, call the `merge` method to merge the PDF files.

```php
// Create an instance of the PDFMerger class
$merger = new PDFMerger\PDFMerger();

// Add PDF files to merge
$merger->addPDF('path/to/file1.pdf');
$merger->addPDF('path/to/file2.pdf', '1-5,7');

// Merge the PDF files and output the result
$merger->merge('browser', 'merged.pdf');
```

## Methods

The `PDFMerger` class provides the following methods:

### __construct()

This is the constructor function for the class, which sets up the environment for the class by finding the path to the `composer` directory and including the necessary files.

### addPDF($filepath, $pages = 'all')

This method adds a PDF file to an array of files with the option to specify which pages to add.

- `$filepath`: The path to the PDF file.
- `$pages` (optional): The pages to add. It can be a comma-separated list of page numbers or page ranges (e.g., `'1,3,4-6'`). The default value is `'all'`.

### merge($outputmode = 'browser', $outputname = 'newfile.pdf')

This method merges multiple PDF files into a single PDF file using the TCPDI library.

- `$outputmode` (optional): The mode in which the merged PDF should be outputted. It can be one of the following values: `'download'`, `'browser'`, `'file'`, or `'string'`. The default value is `'browser'`.
- `$outputname` (optional): The name of the output file. The default value is `'newfile.pdf'`.

## Examples

### Example 1: Merging PDF files and saving the output to a file

```php
$merger = new PDFMerger\PDFMerger();

$merger->addPDF('path/to/file1.pdf');
$merger->addPDF('path/to/file2.pdf', '1-5,7');

$merger->merge('file', 'merged.pdf');
```

### Example 2: Merging PDF files and returning the output as a string

```php
$merger = new PDFMerger\PDFMerger();

$merger->addPDF('path/to/file1.pdf');
$merger->addPDF('path/to/file2.pdf', '1-5,7');

$mergedPDF = $merger->merge('string');
```

## License

This code is provided under the [MIT License](https://opensource.org/licenses/MIT).
