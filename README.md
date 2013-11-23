php-language-identifier
=======================

PHP library to detect language from phrase


## Usage
~~~
$languageIdentifier = new LanguageIdentifier('languages/');
echo "<br />Un rapide renard brun saute par dessus le chien paresseux - " . $languageIdentifier->check('Un rapide renard brun saute par dessus le chien paresseux');
echo "<br />Eine schnelle braune Fuchs springt über den faulen Hund - " . $languageIdentifier->check('Eine schnelle braune Fuchs springt über den faulen Hund');
echo "<br />A quick brown fox jumps over the lazy dog - " . $languageIdentifier->check('A quick brown fox jumps over the lazy dog');
~~~
