# Comandos Laravel:
    
## crear proyecto
    composer create-project --prefer-dist laravel/laravel:^7.0 PROJECT_NAME

    php artisan serve				//levanta el servidor
    php artisan make:controller NOMBRE_DEL_CONTROLADOR
    php artisan make:controller NOMBRE_DEL_CONTROLADOR --resource //mas optimo para generar los metodos automaticamente

# Comandos SQL en laravel:
### crear nueva tabla
    php artisan make:migration create_NOMBRETABLA_table --create=heroes	

### crear las tablas en la base de datos (si ya hay tablas con el mismo nombre no las reemplaza, para eso se usa refresh)
    php artisan migrate 

### actualizar las tablas, cuando hubo cambias, (las borra y las vuelve a crear)
    php artisan migrate:refresh 

### crear una clave foranea entre dos tablas
    php artisan make:migration add_foreign_key_to_NOMBRETABLA --table=NOMBRETABLA

### deshacer cambios en la base de datos, en caso de errores o tablas incompletas.
    php artisan migrate:rollback
    php artisan migrate:rollback --step=NUMERO_DE_PASOS_HACIA_ATRAS


# Modelo: representa un registro en la tabla, es el punto de llegada a la base de datos
## ORM(Modelo Relación de Objetos): nos ayuda a ejecutar una serie de metodos, sin tener que crear las consultas/sentencias sql como tal, 
## recordemos que como es un campo, debe ser en singular el nombre del modelo, y se hubicaran en la raiz de la carpeta app.
    php artisan make:model NOMBRE_SINGULAR

# SEEDERS (DATABASE)
## automatizacion de insercion de datos a la BD, (semillas) el archivo se encontrara en >database>seeds
## pero el que invocara este seeder es el archivo DatabaseSeeder.php(en la misma carpeta), con el método run()
    php artisan make:seeder NOMBRETableSeeder

## despues de crear el seeder haremos los comandos:
    composer dump-autoload
    php artisan db:seed

## lectura de datos de la base de datos y mostrarlos
### se hace en el Controller con un all y se envia a la vista
    public function index(){
        $heroes = Hero::all();
        //le mandamos los datos de la BD a la vista
        return view('admin.heroes.index', ['heroes' => $heroes]);
    }

## Creacion de vistas para mostrar datos, en la plantilla de blade como ya mandamos $heroes
    @foreach ($heroes as $hero)
        <tr>
            <th scope="row">{{ $hero->id }}</th>
            <td>{{ $hero->name }}</td>
        </tr>
    @endforeach

## navegacion a otras vistas (recordar dar de alta el nombre y la ruta en la carpeta >routes>web.php)

    <a class="btn btn-primary my-2" href="{{ route('admin.heroes.create')}}">

## creacion de la vista form para guardar datos en la base de datos.
    views>admin>heroes>create.blade.php

## Guardar datos de un laravel(form) a la base de datos.
### crear funcion store en el controller. y hacer el modelo
    
    $hero = new Hero();
    ###datos obtenidos del form
        $hero->name = $request->input('name');
    ###datos iniciales 
        $hero->coins = 1;
    ###guardar en la BD
        $hero->save();
    ###redireccionar a la pagina deseada.
        return redirect()->route('admin.heroes.index');v

### agregar la ruta 
    Route::post('store', 'HeroController@store')->name('admin.heroes.store');

# ERROR 419 PAGE EXPIRED
## seguridad proporcionada por laravel para no tener forms vulnerables. (investigar mas a fondo en  laravel CSRF)

### agregar esto al form
    @csrf
### buscar el archivo :VerifyCsrfToken y agregar las excepcion de la ruta, para dejar pasar todas se pone un '*'.
    protected $except = [
        'http://127.0.0.1:8000/admin/heroes/store'
        'http://127.0.0.1:8000/*'
    ];

# ERROR: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'name' cannot be null
## pasa porq no relacionamos bien el form con el atributo 'name=CAMPO_BD'


# Modificar un registro de la base de datos, y mostrar una vista con los datos
## se crea la vista
    Route::get('edit/{id}', 'HeroController@edit')->name('admin.heroes.edit');

## se agrega el método al controller,debemos enviar el id del registro a moficiar:
    public function edit($id)
    {
        $hero = Hero::find($id);
        return view('admin.heroes.edit', ['hero' => $hero]);
    }    

## establecemos el valor obtenido delcontroller en la vista ya que lo estamos enviando por parametro:
    <input type="text" class="form-control" required name="name"
    value="{{ $hero->name }}" placeholder="Ingrese el nombre del héroe">

## no olvidemos unir las vistas conlas rutas
    <a class="btn btn-warning me-1" href="{{ route('admin.heroes.edit', ['id' => $hero->id ])}}"> Modificar </a>

## ERROR: Too few arguments to function App\Http\Controllers\HeroController::update(), 1 passed in C:\Users\Ab\Desktop\php\hero\vendor\laravel\framework\src\Illuminate\Routing\Controller.php on line 54 and exactly 2 expected
### olvidamos pasar los parametros en la vista
### routes
    Route::post('update/{id}', 'HeroController@update')->name('admin.heroes.update');
### view
    <form action="{{ route('admin.heroes.update', ['id'=> $hero->id]) }}" method="POST">

# Borrar de la base de datos:
AS en una vista se agrega un form para que dispara la accion,este sera post con un metodo interno DELETE

    <form action="{{ route('admin.heroes.destroy',['id'=>$hero->id]) }}" method="POST">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger">
            Eliminar
        </button>
    </form>

### en las rutas:
Route::delete('destroy/{id}', 'HeroController@destroy')->name('admin.heroes.destroy');

### en el controlador:
    public function destroy($id)
    {
        $hero = Hero::find($id);
        $hero->delete();
        return view('admin.heroes.edit', ['hero' => $hero]);
    }

# OPTIMIZACION DE ROUTE
AS laravel reconoce que es un resource y que tiene los metodos basicos (create, store, edit etc.)
### nos evitamos los prefix y una linea por cada ruta.    
    Route::resource('heroes', 'HeroController');

### ahora cada llamada a ruta: route('path') debe modificarse y eliminar la raiz, en este caso 'admin'
### hay q seguir corrigiendo rutas, para esto es recomendable ver la tabla:

    php artisan route:list

### OJO: parece que en versiones laravel+7 el paso de parametros cambia, ya no necesita el array asociativo( por lo menos en 1 parametro)
### de :
    {{ route('heroes.edit', ['id' => $hero->id]) }}
### a:
    {{ route('heroes.edit', $hero->id) }}

# creación de controllers de una manera mas óptima. **vid78**

    php artisan make:controller NAMEController --resource

## no olvidemos agregarlo en web.php

    Route::resource('items', 'ItemController');

## y cambiar
    route('admin.items') a route('items.index')

## modificar los métodos del Controller, las rutas y basarnos en los métodos que ya teniamos implementados, ya que es un CRUD basico.

# array asocitativo

$report = [
    [
        'name' => "Heroe",
        'counter' => $heroCounter,
        'route' => 'heroes.index',
    ],
    [
        'name' => "Items",
        'counter' => $heroCounter,
        'route' => 'item.index',
    ],
    [
        'name' => "Enemies",
        'counter' => $enemyCounter,
        'route' => 'enemy.index',
    ],
];

# forma de acceder a los datos:
    @foreach ($report as $item)
        $name = $item["name"];

# incluir vistas dentro de vistas en blade
    @include('admin.heroes.form') //ruta real de los directorios.

## condicionales en php dentro de blade.
### en codigo php
    <?php
        if(isset($var)){
            //si existe la $var, hara...
        }
    ?>

### en blade seria:
    @isset($var) si existe hara esto @endisset

# Impresión, revisión de variables

## en php
    var_dump();

## en base de datos
    dd();

# ELOQUENT:Relations
## relacion de tablas, en neustro caso tenemos un heroe que solo puede tener un level (one to one), pero a su vez la tabla levels, puede tener
## X relaciones a distintos heroes (one to many)

### las modificaciones se hacen en el MODELO, ( APP\model.php)
#### App\Hero    
    public function level()
    {
        //referencia a la tabla
        return $this->hasOne("App\Level");
    }
#### App\Level    
#### Modelo al cual tenemos que relacionar(donde esta la llave foraneo) || clave local de ese modelo || llave foranea del modelo donde estamos
    return $this->hasOne("App\Level", "id", "level_id");

##### para validar si funciona podemos imprimir un objeto con dichas llaves
    
    dd($hero->level());
    dd($hero->level);
    dd($hero->level->xp);

 # Modificando la base de datos para agregar imagenes.

    php artisan make:migration NOMBRE_DEL_CAMPO_TABLE -- table=heroes

## agregamos el campo en laravel, en el archivo de database>NOMBRE_DEL_CAMPO y que pueda ser nullable

    public function up()
    {
        Schema::table('heroes', function (Blueprint $table) {
            $table->string('img_path')->nullable();
        });
    }

## con eso ya tenemos el campo en laravel, solo faltaria hacer el "push" a la BD

    php artisan migrate

## ahora en la vista, un form para subir la imagen

    <div class="form-group">
        <label for="img_path">Imagen</label>
        <input type="file" class="form-control" name="img_path" id="img_path">
    </div>

## en el form base, agregar el enctype, ahora admite archivos binarios.

    <form action="{{ route('enemy.store') }}" method="POST" enctype="multipart/form-data">

## en el controler antes del $table->save()
    if($request->hasFile('img_path'));                      // si tiene imagen a subir
    {
        $file = $request->file('img_path');                 // obtenemos el archivo del form
        $name = time() . $file->getClientOriginalName();    // se asigna un nombre unico, basado en la fecha
        $file->move(public_path() . '/images/', $name);     // se almacena en laravel, en la carpeta publica

        $enemy->img_path = $name;                           //se almacena en la base de datos.
    }

## recordemos que al borrar debemos borrarlo tambien de nuestro directorio local, para eso dentro de destroy, en el controller:

    importar: use Illuminate\Support\Facades\File;

    public function destroy($id)
    {
        ...
        $filePath = public_path() . '/images/enemies/' . $enemy->img_path;
        File::delete($filePath);
        ...
    }

# API con postman
## crearemos un APi controller, ya que todo lo que tenemos es local.
    php artisan make:controller APIController 

## el manejo de rutas sera en routes>api.php

    Route::get('/', 'APIController@index');

## y en el controller:
    public function index()
    {
        //estructura de JSON(status, message, data y algunas veces code[404,500,200])
        $res = [
            "status" => "ok",
            "message" => "LA API funciona correctamente",
        ];
        // se envia la respuesta y el codigo php
        return response()->json($res, 200);
    }

## ahora abrimos postman, tiramos a la url: http://127.0.0.1:8000/api y obtenemos la respuesta
    {
        "status": "ok",
        "message": "LA API funciona correctamente"
    }

## agrgamos otro metodo al controller:
    public function getAllHeroes()
    {
        $heroes = Hero::all();

        $res = [
            "status" => "ok",
            "message" => "Lista de heroes",
            "data" => $heroes
        ];
        // se envia la respuesta y el codigo php
        return response()->json($res, 200);
    }

## y ahora la ruta
    Route::get('heroes', 'APIController@getAllHeroes');

## si queremos uno especifico:
    Route::get('heroes/{id}', 'APIController@getHero');

## controller:
    public function getHero($id)
    {
        $hero = Hero::find($id);

        $res = [
            "status" => "ok",
            "message" => "Heroe: " . $hero->name,
            "data" => $hero
        ];
        // se envia la respuesta y el codigo php
        return response()->json($res, 200);
    }

## isset() se usa para saber si una variable es nula o no.
## que pasa si hacemos referencia a un id que no existe...

    public function getHero($id)
    {
        $hero = Hero::find($id);

        if (isset($hero)) {
            $res = [
                "status" => "ok",
                "message" => "Heroe: " . $hero->name,
                "data" => $hero,
            ];
        } else {
            $res = [
                "status" => "error",
                "message" => "el heroe no existe",
            ];
        }
        // se envia la respuesta y el codigo php, aunq no se envie data, la api responde correctamente por eso se envia 200.
        return response()->json($res, 200);
    }

# Consumir funciones desde la API
## lo primero es hacer el método (del controllador) 'static'
    public function runAutoBattle($heroId, $enemyId)
    public static function runAutoBattle($heroId, $enemyId)

## traer el controller al APIController e invocarlo en un metodo dentro del API
    public function runManulBattleSys($heroId, $enemyId)
    {
        $bs = BattleSysController::runAutoBattle($heroId, $enemyId);

        return $bs;
    }

## agregar la ruta.
    //endpoint of battle system
    Route::get('bs/{heroId}/{enemyId}', 'APIController@runManulBattleSys');
    
# APIRESFUL, DESDE CERO

 - se crea el proyecto
 - levantamos nuestro servidor con xampp
 - creamos la base de datos en mysql con el nombre: 'person-api'
 - en laravel modificamos el .env en DB_DATABASE=NAME

## crearemos la tabla persona:
    php artisan make:migration create_people_table --create=people

## vamos a database>la table creada, agregamos los campos de la tabla

    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('firstName');
            $table->string('lastName');
            $table->integer('documentNumber');
            $table->string('city');
            $table->string('country');
            $table->string('street');
            $table->string('number');
            $table->boolean('single');
            $table->timestamps();
        });
    }

## publicamos la tabla en SQL (opcional, podemos borrar las 2 tablas que genera laravel, [users y jobs])
    php artisan migrate

## crearemos nuestro modelo (model y singular y pascal-case)
    php artisan make:model Person

## vamos a app>Person.php y agregamos la tabla a la uqe hace referencia:
    protected $table = "people";

## ahora creamos el controllador con todos los verbs, por eso usamos el --resource, se creara en app>http>controllers
    php artisan make:controller PersonController --resource

## tengamos en cuenta que al tener el controller de esta forma, casi cada metodo representara una interaccion directa
## con la api, index con GET, store con PUT, destroy con DELETE etc. solo queda definir nuestra comunicacion con las
## rutas:
    Route::resource('person', 'PersonController');

## como ejemplo y verificación podemos incluir una vista basica en el index del controlador
    return 'get persona'
## y ahora vamos al browser para visualizarlo, (no olvidemos /api/ )
    http://127.0.0.1:8000/api/person

## si no tenemmos ningun error ahora podemos darle formato a nuestra respuesta.
    public function index()
    {
        $people = Person::all();
        
        $res = [
            'status' => 'ok',
            'message' => 'lista de personas',
            'code' => 1000,
            'data' => $people
        ];

        return $res;
        // return 'people';
    }

## y validar con postman, tirando a la url:
    http://127.0.0.1:8000/api/person

## tambien podemos eliminar los métodos que tengan que ver con la vista, ya que no los usaremos.
- create
- edit

## para validar variables y asi filtrar la accion que se hara, podemos usar dd($var), verificar que da en postman, (pestaña preview)
# y vemos que muestra null, podemos usar el isset (recordemos que se usa para validar si la variable el null y retorna un boolean)

## GET / SHOW

## en codigo:
    public function show($id)
    {
        $person = Person::find($id);

        if(isset($person)){
            $res = [
                'status' => 'ok',
                'message' => 'Obteniendo persona por id: ' . $id,
                'code' => 1001,
                'data' => $person
            ];
        } else {    
            $res = [
                'status' => 'error',
                'message' => 'No se encontro la persona con id: ' . $id,
                'code' => 1011,
                'data' => $person
            ];
        }
        return $res;
    }

## POST / STORE

### para esto necesitamos un objeto json, y eso se logra con:
    $person = $request->json()->all();
    dd($person);
### para visualizarlo en postman, tenemos que hacer un 'POST', opcion body, >RAW luego en Text>json agregamos los datos con el mismo formato
### de lo campos
{
    "firstName": "monse",
    "lastName": "malagon",
    "documentNumber": 3131,
    "city": "mexico",
    "country": "slp",
    "street": "muro",
    "number": "123",
    "single": 0
}
### ahora send y podemos verificar ahi mismo la respuesta, con preview, y deberia ser la misma que enviamos.

### en codigo quedaria algo asi: se recomienda responder con el objeto creado, y su respectivo formato json.
    $person = new Person();
    $person->firstName = $jsonPerson["firstName"];
    $person->lastName = $jsonPerson["lastName"];
    $person->documentNumber = $jsonPerson["documentNumber"];
    $person->city = $jsonPerson["city"];
    $person->country = $jsonPerson["country"];
    $person->street = $jsonPerson["street"];
    $person->number = $jsonPerson["number"];
    $person->single = $jsonPerson["single"];

    $person->save();

    $res = [
        'status' => 'ok',
        'message' => 'persona creada ',
        'code' => 1003,
        'data' => $person
    ];

    return $res;

### podemos simplificar todo esto pasando en el constructor y poner los atributos fillable, en el modelo:
#### controller    
    $jsonPerson = $request->json()->all();
    $person = new Person($jsonPerson);
    $person->save();
#### model
    class Person extends Model
    {
        protected $table = 'people';
        protected $fillable = [
            "firstName",
            "lastName",
            "documentNumber",
            "city",        
            "country",
            "street", 
            "number", 
            "single" 
        ];
    }

## si queremos ocultar campos, en el mismo model agregamos:
    ...
    protected $hidden = [
        "created_at", 
        "updated_at"
    ];
    ...

## DELETE / DESTROY
## para borrar no hay consideraciones especiales, solo generar el codigo y su flujo:
    public function destroy($id)
    {
        $person = Person::find($id);

        if(isset($person)){
            $person->delete();
            $res = [
                'status' => 'ok',
                'message' => 'persona con id: ' . $id . " eliminada",
                'code' => 1004,
            ];
        } else {
            $res = [
                'status' => 'error',
                'message' => 'persona con id: ' . $id . " no encontrada.",
                'code' => 1014,
            ];
        }
        
        return $res;
    }

## PUT / UPDATE

## basicamente funciona como un store, pero con el request put, y solo se mandan los campos a modificar.
## POSTMAN: PUT / url / Body / raw / text > json / agregar:
{
    "firstName": "Viri",
    "street": "tecnologos",
    "number": "69"
}

## en el controller

    public function update(Request $request, $id)
    {
        $person = Person::find($id);

        if(isset($person)){
            $person->update($request->json()->all());
            $res = [
                'status' => 'ok',
                'message' => 'persona con id: ' . $id . " actualizada",
                'code' => 1005,
            ];
        } else {
            $res = [
                'status' => 'error',
                'message' => 'persona con id: ' . $id . " no encontrada para actualizar.",
                'code' => 1015,
            ];
        }
        
        return $res;
    }