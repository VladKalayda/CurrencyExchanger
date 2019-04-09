# CurrencyExchanger
The software is created with:

- JQuery + Bootstrap
- vanilla PHP

PSR-0 coding standard was partially followed for PHP code.

Short manual:
- types.txt is used to fetch the currency types to be converted to UAH;
- if the type.txt file does not exist, it will be generated with the default currency types (USD, EUR)
upon using the API or FILE methods;
- tmp folder is used to store temporary daily JSON data from the PrivatBank API;
- if the tmp folder does not exist, the script will attempt to create it with the 0755 permissions
upon using the API or FILE methods;
