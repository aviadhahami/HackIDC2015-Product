<?php
/**
 * Created by PhpStorm.
 * User: Gal-Rettig
 * Date: 4/8/2015
 * Time: 12:25 AM
 */

//if they DID upload a file...
if($_FILES['photo']['name'])
{
    //if no errors...
    if(!$_FILES['photo']['error'])
    {
        //now is the time to modify the future file name and validate the file
        $new_file_name = strtolower($_FILES['photo']['tmp_name']); //rename file
        if($_FILES['photo']['size'] > (1024000*10)) //can't be larger than 1 MB
        {
            $valid_file = false;
            $message = 'Oops!  Your file\'s size is to large.';
        }

        //if the file has passed the test
        if($valid_file)
        {
            //move it to where we want it to be
            move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/'.$new_file_name);
            $message = 'Congratulations!  Your file was accepted.';
        }
    }
    //if there is an error...
    else
    {
        //set that to be the returned message
        $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['photo']['error'];
    }
}
/*
//you get the following information for each file:
$_FILES['field_name']['name']
$_FILES['field_name']['size']
$_FILES['field_name']['type']
$_FILES['field_name']['tmp_name']*/