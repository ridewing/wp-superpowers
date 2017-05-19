# wp-superpowers

To setup:

1. Run
    
    composer install
    
2. Add autoload to application by adding

    "autoload": {
        "psr-4": {"YOUR_NAMESPACE\\": "application/"}
    }
3. Run 
    
    composer dump-autoload
    