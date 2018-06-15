# ToasterLib
This is javascript toaster library integrated along with Laravel
You need to add this file within config/app.php like as

    'Toaster' => App\Utils\Toaster::class,
  
  after that you simply can use it within your project using like as
  
    use App\Utils\Toaster;
    
   within file you simply use
   
    Toaster::error("Your failure message.")->error('Another message');

instead of 

    withFlashDanger / withFlashSuccess
    
    
And you need to place following code within your main blade file like as

    {!! Toaster::renderToasters() !!}
