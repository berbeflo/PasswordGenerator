# Extending the Password class
```
use berbeflo\PasswordGenerator\Password;

class SpacesPassword extends Password {
    public const POOL_SPACE_CHARS = 4;

    public function __construct(int $options)
    {
        parent::__construct($options);
        $this->registerPoolItemCreator(self::POOL_SPACE_CHARS, function() {
            return " \t";
        });
    }
}

$password = new Spaces(Spaces::OPTION_ALLOW_DOUBLE_CHARS);
$password->add(Spaces::POOL_SMALL_LETTERS);
$password->add(Spaces::POOL_SPACE_CHARS);
$password->require(Spaces::POOL_SPACE_CHARS, 4);
$passwordString = $password->create();
```

# Using the PasswordCreator class
```
use berbeflo\PasswordGenerator\PasswordCreator;

$passwordString = PasswordCreator::create();
$passwordStringWOSpecialChars = PasswordCreator::createAlphaNumeric(8);
```
