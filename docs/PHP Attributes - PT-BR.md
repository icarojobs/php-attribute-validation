# PHP Attributes

### **Referências:**

**Gary Clarke** ([https://www.youtube.com/watch?v=oSo4xbP6ZYo](https://www.youtube.com/watch?v=oSo4xbP6ZYo))

**85 bits (developer)** ([https://youtu.be/jHDcreU-yS4?t=1592](https://youtu.be/jHDcreU-yS4?t=1592))

**DifferDev** ([https://www.youtube.com/watch?v=npUGI3klQqQ](https://www.youtube.com/watch?v=npUGI3klQqQ))


---

Chegado na versão 8 da linguagem, os Attributes vieram para dar mais poderes ao PHP.

Podemos aplicar os **Attributes** em:

- Classes
- Anonymous classes
- Properties
- Constants
- Methods
- Functions
- Closures
- Method and Function parameters

O framework **Symfony** chegou abusando desse recurso utilizando os Attributes em sistemas de rotas, assim como os Decorators do C#.

[https://symfony.com/doc/current/routing.html](https://symfony.com/doc/current/routing.html)

```php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog_list')]
    public function list(): Response
    {
        // ...
    }
}
```

Os Attributes vem pra substituir os antigos “DocBlocks” com as seguintes vantagens:

- Suporte Nativo
- Metadado estruturado
- Performance
- Refatoração segura
- Mais explícito
- Melhor suporte a ferramentas (IDEs, analisadores estáticos, etc.)

Um ótimo exemplo de uso dos novos **Attributes** é quando utilizamos **DTO** (**Data Transfer Objects**) que são objetos que precisam estar validados antes de serem persistidos no banco de dados.

Vamos criar um pequeno projeto pra estudar os famosos Attributes do PHP.

```bash
cd ~/Projects/tio-jobs
mkdir php-attributes
cd php-attributes
composer init
...
touch .gitignore
```

**.gitignore:**

```
.DS_Store
.idea
/vendor
```

Pra facilitar a nossa vida, vamos mudar o `namespace` da nossa aplicação para `App` :

```json
{
	...
	"autoload": {
		"psr-4": {
			"App\\": "src/"
		}
	}
}
```

Vamos realizar um `dumpautoload` para recarregar nosso novo namespace:

```bash
composer du

# or

composer dumpautoload
```

Vamos instalar algumas dependências para nos ajudar:

```bash
composer require symfony/var-dumper --dev
```

Esse comando vai nos permitir utilizar o helper `dump()` ou `dd()` para debugarmos valores.

### Criando nosso DTO

Vamos criar um diretório chamado `DTO` dentro de `/src` e, em seguida, criar a classe `UserRegistration.php` .

Esse DTO que vamos criar é para simular o envio de um formulário do cadastro de um usuário no sistema.

**Estrutura de diretórios:**

/src

…/DTO

……/UserRegistration.php

**UserRegistration.php:**

```php
<?php

declare(strict_types=1);

namespace App\DTO;

readonly final class UserRegistration
{
    public function __construct(
        public string $user,
        public string $email,
    ) { }
}
```

Agora, vamos criar nosso arquivo de inicialização. Vamos chamar de `app.php` que ficará na raiz do projeto:

**app.php:**

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\DTO\UserRegistration;

$userRegistration = new UserRegistration('Admin', 'admin@admin.com');

dd($userRegistration);
```

**Output:**

```bash
php app.php

App\DTO\Registration {#2
    +user: "Admin"
    +email: "admin@admin.com"
}
```

**OBS.:** Utilizamos `readonly` para deixarmos as `propriedades imutáveis` , ou seja, só é permitido informar os valores no ato da criação/instanciação do objeto, depois ja era!

### Criando Attribute personalizado

Vamos criar nosso primeiro **Attribute** personalizado, que vai funcionar como uma **Validation** **Rule** para a classe `UserRegistration` .

Vamos criar um novo diretório chamado `Validations` e, dentro de Validations outro diretório chamado `Rules` , ambos dentro de `src` . Por fim, vamos criar a classe `Required.php` que vai funcionar como `Attribute` para validar nossos campos.

**Estrutura de diretórios:**

/src

…/Validations

……/Rules 

………/Required.php

………/Length.php 

………/Email.php

**Required.php:**

```php
<?php

declare(strict_types=1);

namespace App\Validations\Rules;

#[\Attribute]
class Required
{

}
```

Vamos aplicar esse **Attribute** nas **propriedades** da classe `UserRegistration` :

```php
<?php

declare(strict_types=1);

namespace App\DTO;

use App\Validations\Rules\Required;

readonly final class UserRegistration
{
    public function __construct(
        #[Required]
        public string $user,

        #[Required]
        public string $email,
    ) { }
}
```

Agora, vamos precisar de uma classe que será responsável pela validação. Vamos chamar ela de `Validator.php` que vai ficar dentro de `/src/Validations` :

**Validator.php:**

```php
<?php

declare(strict_types=1);

namespace App\Validations;

class Validator
{
    private array $errors = [];

    public function validate(object $object): void
    {
        $reflector = new \ReflectionClass($object);

        dd($reflector);  // vamos ver o que retorna...
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
```

Antes de executar o código, vamos alterar nosso `app.php` :

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\DTO\UserRegistration;
use App\Validations\Validator;

$userRegistration = new UserRegistration('', ''); // forçar erro!!!

$validator = new Validator();
$validator->validate($userRegistration);
$errors = $validator->getErrors();

print_r($errors);
```

Agora vamos executar o código e ver se o código vai retornar a `ReflectionClass`:

```bash
php app.php

ReflectionClass {#5
	+name: "App\DTO\UserRegistration"
	modifiers: "readonly"
	...
}
```

Reparem o poder da `ReflectionClass` do PHP, ele consegue demonstrar tanto **propriedades** (**ReflectionProperty**), quando **atributos** (**ReflectionAttribute**) de uma classe. Isso da um poder imenso para o programador se ele souber utilizar!

Beleza! Agora, antes de continuar a implementação do `Validator`, vamos precisar criar 2 interfaces, uma chamada `ValidatorInterface` e `ValidationRuleInterface` , porque no momento que iterarmos nas propriedades da instância da `ReflectionClass`, vamos querer apenas **Attribute** que implementa a interface `ValidationRuleInterface` , que obriga a implementar o método `getValidator()` que, obrigatóriamente, retorna uma `ValidatorInterface`. Parece complexo, mas vamos fazer com calma que vai dar tudo certo.

Vamos começar criando a interface `ValidatorInterface` , que vai ficar localizada em `/src/Validations/Rules/Contracts` :

**ValidationInterface.php:**

```php
<?php

namespace App\Validations\Rules\Contracts;

interface ValidatorInterface
{
    public function validate(mixed $value);
}
```

E, também, vamos deixar pronto a `LenthValidatorInterface` para validação de tamanhos:

**LengthValidatorInterface.php:**

```php
<?php

namespace App\Validations\Rules\Contracts;

interface LengthValidatorInterface
{
    public function validate(mixed $value, int $min, int $max);
}
```

Agora, vamos criar a `ValidationRuleInterface` que vai ficar no mesmo diretório `/src/Validations/Rules/Contracts` .

**ValidationRuleInterface.php:**

```php
<?php

namespace App\Validations\Rules\Contracts;

interface ValidationRuleInterface
{
    public function getValidator(): ValidatorInterface|LengthValidatorInterface;
}
```

**DICA:** Alguns nomes de diretórios para referenciar Interfaces e Trais (muito usados no Laravel):

`Contracts` : Interfaces

`Concerns` : Traits

### Criando classes de validação

Agora, precisamos criar nossas “classes de validações”, por exemplo: `RequiredValidator` , `LengthValidator` , `EmailValidator` , etc. Pra esse vídeo (e pra não ficar muito complexo, vamos criar apenas o `RequiredValidator` .

Bom, então vamos criar um novo diretório chamado `Validators` que vai ficar em `/src/Validations/Validators` e, dentro dele, a nossa classe de validação:

**RequiredValidator.php:**

```php
<?php

declare(strict_types=1);

namespace App\Validations\Validators;

use App\Validations\Rules\Contracts\ValidatorInterface;

class RequiredValidator implements ValidatorInterface
{
    public function validate(mixed $value): bool
    {
        return !empty($value);
    }
}
```

### Implementando o método getValidator() na classe de attributo Required

Agora sim estamos prontos para implementar o método `getValidator()` . Bora lá!

**Required.php:**

```php
<?php

declare(strict_types=1);

namespace App\Validations\Rules;

use App\Validations\Rules\Contracts\ValidationRuleInterface;
use App\Validations\Rules\Contracts\ValidatorInterface;
use App\Validations\Validators\RequiredValidator;

#[\Attribute]
class Required implements ValidationRuleInterface
{
    public function getValidator(): ValidatorInterface
    {
        return new RequiredValidator();
    }
}
```

### Finalizando o método validate() da classe Validator

Agora, vamos completar continuar a implementação do método `validate()`:

**Validator.php:**

```php
<?php

declare(strict_types=1);

namespace App\Validations;

use App\Validations\Rules\Contracts\ValidationRuleInterface;

class Validator
{
    private array $errors = [];

    public function validate(object $object): void
    {
        $reflector = new \ReflectionClass($object);

        foreach ($reflector->getProperties() as $property) {
            $attributes = $property->getAttributes(
                name: ValidationRuleInterface::class,
                flags: \ReflectionAttribute::IS_INSTANCEOF,
            );

            foreach ($attributes as $attribute) {
                $validator = $attribute->newInstance()->getValidator();

                if (!$validator->validate($property->getValue($object), ...$attribute->getArguments())) {
                    $this->errors[$property->getName()][] = sprintf(
                        "Invalid value for '%s' using '%s' validation.",
                        $property->getName(),
                        $attribute->getName()   
                    );
                }
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
```

Agora, vamos executar nosso código novamente e verificar que nossa validação está funcionando perfeitamente:

**Output:**

```bash
php app.php

Array
(
    [user] => Array
        (
            [0] => Invalid value for 'user' using 'App\Validations\Rules\Required' validation.
        )

    [email] => Array
        (
            [0] => Invalid value for 'email' using 'App\Validations\Rules\Required' validation.
        )

)
```

---

### Criando validação de emails

Agora que temos toda estrutura de validação pronta utilizando meta-programação (Attributes), vamos criar uma nova validação para emails.

Vamos começar da classe de validação `EmailValidator` :

**/src/Validations/Validators/EmailValidator.php:**

```php
<?php

declare(strict_types=1);

namespace App\Validations\Validators;

use App\Validations\Rules\Contracts\ValidatorInterface;

class EmailValidator implements ValidatorInterface
{
    public function validate(mixed $value): bool
    {
        return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
```

Agora sim estamos prontos para implementar o método `getValidator()` (que é nossa Rule) na nova classe de atributo `Email.php` :

**/src/Validations/Rules/Email.php:**

```php
<?php

declare(strict_types=1);

namespace App\Validations\Rules;

use App\Validations\Rules\Contracts\ValidationRuleInterface;
use App\Validations\Rules\Contracts\ValidatorInterface;
use App\Validations\Validators\EmailValidator;

#[\Attribute]
class Email implements ValidationRuleInterface
{
    public function getValidator(): ValidatorInterface
    {
        return new EmailValidator();
    }
}
```

Por fim, precisamos adicionar o novo atributo `Email` na classe `UserRegistration`:

**/src/DTO/UserRegistration.php:**

```php
<?php

declare(strict_types=1);

namespace App\DTO;

use App\Validations\Rules\Required;
use App\Validations\Rules\Email;

readonly final class UserRegistration
{
    public function __construct(
        #[Required]
        public string $user,

        #[Required]
        #[Email]
        public string $email,
    ) { }
}
```

Vamos mudar um pouco nosso `app.php` :

```php
$userRegistration = new UserRegistration('Tio Jobs', 'nada-a-ver');
```

E agora, vamos testar e verificar o funcionamento:

**Output:**

```bash
php app.php

Array
(
    [email] => Array
        (
            [0] => Invalid value for 'email' using 'App\Validations\Rules\Email' validation.
        )

)
```

---

### Criando validação para mínimo e máximo de caracteres

Essa validação exige um pouco mais de atenção, mas nada complexo. Vamos precisar ter um método construtor em nosso atributo `Length` . Vamos começar do validator, no caso, `LengthValidator` :

**/src/Validations/Validators/LengthValidator.php:**

```php
<?php

declare(strict_types=1);

namespace App\Validations\Validators;

use App\Validations\Rules\Contracts\LengthValidatorInterface;

class LengthValidator implements LengthValidatorInterface
{
    public function validate(mixed $value, int $min, int $max): bool
    {
        return mb_strlen($value) >= $min && mb_strlen($value) <= $max;
    }
}
```

Agora sim estamos prontos para implementar o método `getValidator()` (que é nossa Rule) na nova classe de atributo `Length.php`:

**/src/Validations/Rules/Length.php:**

```php
<?php

declare(strict_types=1);

namespace App\Validations\Rules;

use App\Validations\Rules\Contracts\ValidationRuleInterface;
use App\Validations\Rules\Contracts\LengthValidatorInterface;
use App\Validations\Validators\LengthValidator;

#[\Attribute]
class Length implements ValidationRuleInterface
{
    public function __construct(
        public int $min,
        public int $max,
    ) { }

    public function getValidator(): LengthValidatorInterface
    {
        return new LengthValidator();
    }
}
```

Por fim, precisamos adicionar o novo atributo `Length` na classe `UserRegistration`:

**/src/DTO/UserRegistration.php:**

```php
<?php

declare(strict_types=1);

namespace App\DTO;

use App\Validations\Rules\Required;
use App\Validations\Rules\Email;
use App\Validations\Rules\Length;

readonly final class UserRegistration
{
    public function __construct(
        #[Required]
        #[Length(min: 10, max: 255)]
        public string $user,

        #[Required]
        #[Email]
        public string $email,
    ) { }
}
```

Vamos testar e verificar o funcionamento:

```bash
php app.php

Array
(
    [user] => Array
        (
            [0] => Invalid value for 'user' using 'App\Validations\Rules\Length' validation.
        )

)
```

### FIM!