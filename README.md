# Sanitizer

Sanitizer é um package para higienizar os dados de sua aplicação em uma camada separada.  

Muitas vezes é necessário uma camada intermediária na aplicação, com o objetivo de realizar a higienização de dados,
estes geralmente provenientes de formulários do usuário. Por exemplo, você deseja realizar a limpeza dos espaços em
branco dos campos do formulário antes de enviar os dados para o banco de dados.

Este pacote tem como base a excelente aula do Jeffrey Way sobre Sanitizers.
[https://github.com/laracasts/Sanitizers-and-PHPSpec](https://github.com/laracasts/Sanitizers-and-PHPSpec)

# Instalação

O pacote pode ser instalado através do <a href="https://getcomposer.org/" target="_blank">composer</a>.
Para que o package seja adicionado automaticamente ao seu arquivo `composer.json` execute o seguinte comando:

```shell
composer require lukzgois/sanitizer
```

ou se preferir, adicione o seguinte trecho manualmente:

```json
{
	"require": {
		"lukzgois/sanitizer" : "0.1.x"
	}
}
```

# Utilização

Criar uma classe extendendo ```Lukzgois\Sanitizer\Sanitizer```

```php
    use Lukzgois\Sanitizer\Sanitizer;

    class SanitizeRequest extends Sanitizer {
 
        public function rules()
        {
            return [
                'first_name' => 'trim|ucword',
                'last_name' => 'trim|ucwords'
            ];
        }
    }
```

Utilizar o método sanitize com o array de dados a ser validado:

```php
    $data = ['name' => '  john doe '];
    $sanitizer = new SanitizeRequest();
    $sanitizedData = $sanitizer->sanitize($data);
    var_dump($sanitizedData); // ['name' => 'John Doe']
```

Você pode sobrescrever as regras de higienização passando um array como segundo parâmetro da função sanitize():

```php

    $sanitizer->sanitize($data, ['name' => 'strtoupper']);
```

Para utilizar métodos personalizados na classe basta criá-los com o prefixo "sanitize":

```php
    use Lukzgois\Sanitizer\Sanitizer;

    class SanitizeRequest extends Sanitizer {
 
        public function rules()
        {
            return [
                'phone' => 'phone',
            ];
        }
        
        public function sanitizePhone($value)
        {
        	return str_replace('-','', $value);
        }
    }
```

Você tambem pode utilizar outras classes para realizar a higienização, basta para isso indicar o caminho completo para essa classe. Por padrão o package irá procurar pela função sanitize() nessa classe. Para utilizar outra função basta indicar com um ```@``` após o nome da classe:

```php
    use Lukzgois\Sanitizer\Sanitizer;

    class SanitizeRequest extends Sanitizer {
 
        public function rules()
        {
            return [
                'first_name' => '\App\Sanitizers\NameSanitizer', // sanitize()
                'last_name' => '\App\Sanitizers\NameSanitizer@lastName' // lastName()
            ];
        }
    }
```

Também é possível passar argumentos para as funções personalizadas da seguinte maneira:

```php
	use Lukzgois\Sanitizer\Sanitizer;

    class SanitizeRequest extends Sanitizer {
 
        public function rules()
        {
            return [
                'first_name' => '\App\Sanitizers\MinValueSanitizer:100', // sanitize()
            ];
        }

    }
    
    class \App\Sanitizers\MinValueSanitizer {
    
    	public function sanitize($value, $min)
        {
        	return $value < $min ? $min : $value;
        }
    
    }
```

Por padrão, o package conta com uma função customizada, a função ```default```, que serve para definir um valor padrão para um campo caso este venha nulo ou vazio, por exemplo: 


```php
	use Lukzgois\Sanitizer\Sanitizer;

    class SanitizeRequest extends Sanitizer {
 
        public function rules()
        {
            return [
            	'name' => 'trim|ucwords',
                'company' => 'default:1'
            ];
        }

    }
    
```

```php
    $data = ['name' => '  john doe '];
    $sanitizer = new SanitizeRequest();
    $sanitizedData = $sanitizer->sanitize($data);
    var_dump($sanitizedData); // ['name' => 'John Doe', 'company' => 1]
```
