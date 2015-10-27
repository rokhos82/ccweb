var it = {};

it.debug = {};
it.debug.on = true;
it.debug.log = function(entry) {
	if(!it.debug.on)
		return;
	if(window.console && window.console.log) {
		window.console.log(entry);
	}
	else
		alert(entry);
};

it.navigation = [
	{
		"title": "Home",
		"link": "/gov-admin/information-technology"
	},
	{
		"title": "Subsection 1",
		"link": "/gov-admin/information-technology/sub1",
		"items": [
			{
				"title": "Item 1"
			},
			{
				"title": "Item 2"
			}
		]
	}
];

it.initNavigation = function(rootID) {
	var root = document.getElementById(rootID);
	var ul = document.createElement("ul");
	root.appendChild(ul);

	for(var i in it.navigation) {
		it.buildMenu(it.navigation[i],ul);
	}
};

it.buildMenu = function(struct,dom) {
	if(struct) {
		var name = struct.title;
		it.debug.log("Adding " + name + " to menu.");
		var li = document.createElement("li");
		var a = document.createElement("a");
		var text = document.createTextNode(name);
		a.href = struct.link;
		a.appendChild(text);
		li.appendChild(a);
		dom.appendChild(li);

		if(struct.items) {
			var ul = document.createElement("ul");
			dom.appendChild(ul);
			for(var i in struct.items) {
				it.buildMenu(struct.items[i],ul);
			}
			li.appendChild(ul);
		}
	}
}