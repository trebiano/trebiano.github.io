/*******************************************************************************
*         SecurityImages v2.0 Copyright 2005 Walter Cedric
*		www.waltercedric.com
*
*    This file is part of SecurityImages component for Joomla.
*
*    com_securityimages is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License, or
*    (at your option) any later version.
*
*    com_securityimages is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with com_securityimages; if not, write to the Free Software
*    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*******************************************************************************/

var myReloadCounter;

function getElement(elementName)
{
    try {
     return document.getElementsByName(elementName)[0]
    }
    catch(e)
    {
     alert('Exception '+e);
    };
}

function SecurityImagesInit()
{
    myReloadCounter=0;
}

function SecurityImagesNew(packageName, packageNameTry,packageNameReload )
{
	myImageGeneratorSource = getElement(packageName).src;   
    myImage= getElement(packageName);
    myUserInputBox=  getElement(packageNameTry);
    myReloadHiddenField = getElement(packageNameReload);
	
    //reset input box
    myUserInputBox.value="";
    
    //ask server for a new picture, reload will be use to determine entry shift into DB
    myImage.src = myImageGeneratorSource+"&reload="+myReloadCounter;
    myImage.height = myImage.height;
    myImage.width = myImage.width;
    
    //reload counter submitted for checker
    myReloadHiddenField.value=myReloadCounter;
    myReloadCounter++;
    myUserInputBox.focus();
}