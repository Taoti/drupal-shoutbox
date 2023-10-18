# Shoutbox for drupal 9 or 10
## Disclaimer
This is a work in progress module for drupal 9 or 10.

It allows to create multiple shoutboxes, and provide a block to see it.

## Installation and configuration

###Composer file
under repositories add
```
{
"type": "vcs",
"url": "https://github.com/Taoti/drupal-shoutbox.git"
}
```

under required
```
"taoti/shoutbox": "dev-master",
```

### Downloading
Via composer 
```
composer require taoti/shoutbox
```

### Enable the module
Through the UI : /admin/module

or via drush : `drush en shoutbox`

### Configure the permissions
Through the UI : `/admin/people/permissions`

### Create your first shoutbox
Through the UI : `/admin/content/shoutbox`

You can access it via `/shoutbox/ID` eg : `/shoutbox/1`.

You can also place a block and select the shoutbox you want to display
