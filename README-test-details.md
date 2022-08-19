Note: 
For mail: You need to enter your smtp detail in env file.
The url of API,Show the list of events with UI: http://localhost/laravel/laravel/event/public/api/v1/events-us

Created File list: 
View: resources\views\
	---event_add_edit.blade.php
	---event_create_mail.blade.php
	---events_list.blade.php
	
routes: 
	---web.php
	---api.php

app\Http\Controllers\ 
		--Events.php

Model: \app\Models
	 ---Event.php
	 
Mail:  app\Mail
	--- NotifyCreateEvent.php
