function hideToolbar(item)
{
   obj = document.getElementById(item)
   obj.style.visibility = "hidden";
}

function showToolbar(listitem)
{
   obj = document.getElementById(listitem);
   objHeight = obj.offsetHeight;
   objWidth = obj.offsetWidth;
   objPos = findPos(obj);
   
   toolbarObj = document.getElementById(listitem + "_tool");
   toolbarHeight = toolbarObj.offsetHeight;
   toolbarPosX = objPos[0] + objWidth;
   toolbarPosY = objPos[1] + (objHeight/2) - (toolbarHeight/2);
   
   //position the toolbar
   toolbarObj.style.position = "absolute";
   toolbarObj.style.visibility = "visible";
   toolbarObj.style.left = toolbarPosX + "px";
   toolbarObj.style.top = toolbarPosY + "px";
}