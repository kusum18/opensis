<?php
function browser()
{
if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko') )
{
   if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape') )
   {
     $browser = 'Netscape';
   }
   else if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') )
   {
     $browser = 'Firefox';
   }
   else
   {
     $browser = 'Mozilla';
   }
}
else if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') )
{
   if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') )
   {
     $browser = 'Opera';
   }
   else
   {
     $browser = 'IE';
   }
}
else
{
   $browser = 'Others browsers';
}

return $browser;
}

?>