Transactions
==========

Transactions store information about a request: its success or failure, associated error messages,
and links to any created or updated objects.

Creating the migration file for transaction entities
------------------------
Simply use ```php app/console doctrine:generate:entities``` in the root of the project to generate the SQL schema for
 the transaction entities.

Setting up controllers
------------------------
An annotation will need to be added to the header of a controller class. Two properties of this annotation
will need to be set: model and relatedRoute. The model property will specify the individual entity/model scope of
the controller. The relatedRoute property will specify the route used in retrieval of the entity/model. See the
following example for a hypothetical library bundle.

```
/**
 * Book REST Controller
 *
 * @author Ryan Wood <ryan.wood@opticsplanet.com>
 *
 * @EcentriaAnnotation\Transactional(
 *      model="LibraryBundle\Entity\Book",
 *      relatedRoute="get_book"
 * )
 *
 */
class BookController extends FOSRestController
{
```
You can also add an annotation to an action in your controller if you would like to disable transaction support for
that action.

```
* @EcentriaAnnotation\AvoidTransaction()
*/
public function fireAuthorAction(Author $author)
```

Setting up transactional entities
------------------------
Any entity you want to support transactions will need to implement the CrudEntityInterface. If you want full error
support in transactions you will also need to implement the ValidatableInterface.

Using transactions
------------------------
A link to a service transaction will be provided in the response of your requests. Each transaction will also be
 retrievable at any time from the transaction controller. See the following example of a transaction response.

```
"service-transaction": {
    "id": "275338b2-6002-40f4-8ee4-7073adcfc32e",
    "model": "LibraryBundle\\Entity\\Book",
    "related_ids": {
        "category": "thrillers",
        "id": "203"
    },
    "related_route": "get_book",
    "method": "POST",
    "request_source": "REST",
    "request_id": "0.28570900 1435773015",
    "created_at": "2015-07-01T12:50:15-0500",
    "updated_at": "2015-07-01T12:50:15-0500",
    "status": 409,
    "success": false,
    "messages": [
         "errors": [
             {
                 "code": 1,
                 "message": "You must assign an author to any newly created book",
                 "context": "data",
                 "key": "author"
             }
         ]
    ],
    "_links": {
        "related": {
            "href": "http:\/\/libraryresource.org\/book\/thrillers\/203"
        },
        "self": {
            "href": "http:\/\/libraryresource.org\/transaction-service\/275338b2-6002-40f4-8ee4-7073adcfc32e"
        }
    }
}
```