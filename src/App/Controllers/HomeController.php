<?PHP

declare(strict_types=1);
 
namespace App\Controllers;

use App\Services\TransactionService;
use Framework\TemplateEngine;

class HomeController
{
    
    public function __construct(private TemplateEngine $view, private TransactionService $transactionService )
    {
    }
    public function  home()
    {
        ## adding Pagination
        # retrieving query parameters (the page number) from the url if exist otherwise the page number is 1
        $page = $_GET['p'] ?? 1 ;

        $page = (int) $page;
        $length = 3 ;
        $offset = ($page - 1 ) * $length;

        $searchTerm = $_GET['s'] ?? null ;

        [$transactions, $count] = $this->transactionService->getUserTransactions($length , $offset);

        $lastPage = ceil($count / $length);

        ## generating a page number link
        $pages = $lastPage ? range(1, $lastPage) : [] ;

        $pageLinks = array_map(
            fn($pageNum) => http_build_query([
                'p' => $pageNum,
                's' => $searchTerm
            ]),
            $pages
        );

        echo $this->view->render("/index.php",
        [
           'transactions' => $transactions,

           ## adding 0Previous link page
           'currentPage' => $page,
           'previousPageQuery' => http_build_query([
                'p' => $page - 1 ,
                's' => $searchTerm
           ]),

           ## adding 0Previous link page
           'lastPage' => $lastPage,
           'nextPageQuery' => http_build_query([
                'p' => $page + 1 ,
                's' => $searchTerm
           ]),

           ## crating page number links
           'pageLinks' => $pageLinks,
           'searchTerm' => $searchTerm
        ]);
    }
}
