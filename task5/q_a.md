# A) Explain this code

the code is a cron job ran by laravel task scheduler and it schedules app:example-command to run
every hour, it prevents overlapping and starting a new instance of the command if one is runninng,
ensure running on one server and executes the command in the background so it doesn’t block other tasks
useful for heavy or long-running tasks


# B) What is the difference between the Context and Cache Facades? Provide examples to illustrate your explanation.

the context facade gives infos about the current runtime (loggedin user,request,session) 
the cache facade is for storing data temporarily or persistentily to avoid repeated requests to db ro api

```bash
Cache::put('settings', $data, 3600); // 1 hour 
$settings = Cache::get('settings');
////////
Cache::forever('settings', $data); // permanent
==================================

Context::get('request');
$userId = Context::user()->id;
```



# c) What’s the difference between $query->update(), $model->update(), and $model->updateQuietly() in Laravel, and when would you use each?


$query->update() : direct db updates no model instance required
$model->update() : updates the model instance with event and timestamts udpating:wa!
$model->updateQuietly() : updates the model instance without event and timestamps udpating

i use $query->update for bulk updates and model->update for single/normal updates with events and the quietly when
i want to by pass observer without side-effects



# d) Explain Laravel’s Cache flexible function and provide a scenario that can be good for using it.

checks if a value exists in the cache if it does it returns it, if it doesnt it runs a function to compute the value and  stores it in the cache 
for a set of time and then returns it

useful for expensive data or fetches heavy calculation

for example a page showing top selling products its slow to fetch all the products from the db so we can cache it for an hour so
    repeated are fast and the db isnt overloaded
