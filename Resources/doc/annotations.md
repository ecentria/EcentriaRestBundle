Annotations
===========

Transactional
-------------

Used for controller to enable transaction system.
    
**model**

    Every controller must work with defined resource.
    Model parameter should be equal to full path to your
    entity that current controller works with.

**relatedRoute**

    Every model should have route leading to get action.
    Related route parameter must be equal to current route name.

Example:

```php
use Ecentria\Libraries\CoreRestBundle\Annotation as EcentriaAnnotation;

/**
 * @EcentriaAnnotation\Transactional(
 *   model="Path to you entity",
 *   relatedRoute="your_get_entity_route"
 * )
 */
```      
      
AvoidTransaction
----------------

Used for controller action to avoid creating transaction.

```php
use Ecentria\Libraries\CoreRestBundle\Annotation as EcentriaAnnotation;

/**
 * @EcentriaAnnotation\AvoidTransaction()
 */
```
        
PropertyRestriction
-------------------

Used for model (entity) property to avoid update or create.
As parameter it gets array of actions: {“update”, “create"}

```php
use Ecentria\Libraries\CoreRestBundle\Annotation as EcentriaAnnotation;

/**
 * @EcentriaAnnotation\PropertyRestriction({"update", "create"})
 */
```
         