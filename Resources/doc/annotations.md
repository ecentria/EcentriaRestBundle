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

**writeStatusCodes**

    Model may have this config option for copying status codes
    from the transaction to the view (false by default).

Example:

```php
use Ecentria\Libraries\EcentriaRestBundle\Annotation as EcentriaAnnotation;

/**
 * @EcentriaAnnotation\Transactional(
 *   model="Path to you entity",
 *   relatedRoute="your_get_entity_route",
 *   writeStatusCodes=true
 * )
 */
```

RelatedRouteForAction
---------------------

Used for controller action to override the related route set at the class level. This is useful if an action in the controller works with a different model than the primary model for the class so it needs a different route for the get action.

```php
use Ecentria\Libraries\EcentriaRestBundle\Annotation as EcentriaAnnotation;

/**
 * @EcentriaAnnotation\RelatedRouteForAction(routeName="get_entity")
 */
public function getEntityAction(Request $request)
```
      
AvoidTransaction
----------------

Used for controller action to avoid creating transaction.

```php
use Ecentria\Libraries\EcentriaRestBundle\Annotation as EcentriaAnnotation;

/**
 * @EcentriaAnnotation\AvoidTransaction()
 */
```
        
PropertyRestriction
-------------------

Used for model (entity) property to avoid update or create.
As parameter it gets array of actions: {“update”, “create"}

```php
use Ecentria\Libraries\EcentriaRestBundle\Annotation as EcentriaAnnotation;

/**
 * @EcentriaAnnotation\PropertyRestriction({"update", "create"})
 */
```

Blamable
-------------------

**Blameable** behavior will automate the update of username or user reference fields
on your Entities or Documents. It works through annotations and can update
fields on creation, update, property subset update, or even on specific property value change. [More details](https://github.com/Atlantic18/DoctrineExtensions/edit/master/doc/blameable.md).

If you map the blame onto a string field, this extension will try to assign the user name.
If you map the blame onto a association field, this extension will try to assign the user
object to it.

Blamable user name is set from the request headers *'X-EC-API-REQUEST-USER'* or *'From'*.

### Blameable annotations:
- **@Gedmo\Mapping\Annotation\Blameable** this annotation tells that this column is blameable
by default it updates this column on update. If column is not a string field or an association
it will trigger an exception.

Available configuration options:

- **on** - is main option and can be **create, update, change** this tells when it
should be updated
- **field** - only valid if **on="change"** is specified, tracks property or a list of properties for changes
- **value** - only valid if **on="change"** is specified and the tracked field is a single field (not an array), if the tracked field has this **value**
then it updates the blame

``` php
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Gedmo\Blameable(on="update")
 * @ORM\Column(type="string")
 */
```
