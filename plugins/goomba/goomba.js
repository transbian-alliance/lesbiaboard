var goomba, goomPos, goomDir, goomStep, goomStomped, goomAudio;
function startGoomba()
{
	goomba = document.getElementById("goomba");
	if(goomba == null)
	{
		setTimeout("startGoomba();", 100);
		return;
	}
	if(Math.random() > 0.5)
	{
		goomPos = getWidth();
		goomDir = -1;
	}
	else
	{
		goomPos = -15;
		goomDir = 1;
	}
	goomStep = 0;
	goomAudio = new Audio("plugins/goomba/goomba.ogg");
	setTimeout("moveGoomba();", 100);
}

function moveGoomba()
{
	if(goomStomped)
		return;
	goomPos += goomDir;
	if(goomPos > getWidth() || goomPos < -16)
	{
		if(Math.random() > 0.5)
		{
			goomPos = getWidth();
			goomDir = -1;
		}
		else
		{
			goomPos = -15;
			goomDir = 1;
		}
	}
	goomba.style.left = goomPos + "px";
	goomStep = (goomStep + 1) % 2;
	goomba.style.backgroundPosition = "-" + (goomStep * 16) + "px 0px";
	setTimeout("moveGoomba();", 100);
}

function stompGoomba()
{
	goomStomped = 1;
	goomba.style.backgroundPosition = "-32px 0px";
	goomAudio.play();
	setTimeout("removeGoomba();", 500);
}

function removeGoomba()
{
	goomba.style.display = "none";
}

function getWidth()
{
	if (self.innerHeight)
		return self.innerWidth;
	else if (document.documentElement && document.documentElement.clientHeight)
		return document.documentElement.clientWidth;
	else if (document.body)
		return document.body.clientWidth;
}

window.addEventListener("load", startGoomba, false);
