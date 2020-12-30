# wordpress-posts-collection
Create posts and post types collection for WordPress


# Install
```
composer require ramphor/wordpress-posts-collection
```


# Setup

```
use Ramphor\Collection\CollectionManager;
use Ramphor\Collection\DB as CollectionDB;setup';


```
Activation hook
```
register_activation_hook(__PLUGIN_FILE__, array(CollectionDB::class, 'setup'));
```

Main hooks
`plugins_loaded` or `after_setup_theme` or any your favorite hook before `wp` hook
```
CollectionManager::getInstance();
```
