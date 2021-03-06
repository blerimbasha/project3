<?php
/**
 * Created by PhpStorm.
 * User: blerimi_v
 * Date: 1/14/2019
 * Time: 11:15 PM
 */

namespace App\Controller;


use App\Entity\Regions;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Entity\UserNotifications;
use App\Form\GuestBookingType;
use App\Form\RestaurantType;
use phpDocumentor\Reflection\Types\This;
use App\Form\UserType;
use App\Repository\RestaurantTypeRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use function MongoDB\BSON\toJSON;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Restaurants
 * @package App\Controller
 * @Route("/restaurants")
 */
class Restaurants extends Controller
{
    /**
     * @Route("/", name="restaurants")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $repository = $this->getDoctrine()->getManager();
        $restaurants =$repository->getRepository('App:Bookings')->findByBooking(
            $request->query->get('search')['region'],
            $request->query->get('search')['name'],
            $request->query->get('search')['from_date'],
            $request->query->get('search')['to_date'],
            $request
        );

        $pagination = $paginator->paginate(
            $restaurants,
            $request->query->getInt('page', 1),
            20
        );


        return $this->render('restaurants/index.html.twig', [
            'restaurants' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="new_restaurant")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $restaurant = new Restaurant();
        $lastId = $em->getRepository('App:Restaurant')->lastRestarantId();
        if ($lastId != null) {
            $currentId = $lastId->getId() + 1 ;
        }

        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $fileCover = $restaurant->getCoverPath();
             $image1 = $restaurant->getImage1();
             $image2 = $restaurant->getImage2();
             $image3 = $restaurant->getImage3();
             $image4 = $restaurant->getImage4();


             if ($fileCover != null) {
                 $fileNameCover = $this->generateUniqueFileName().'.'.$fileCover->guessExtension();
                 $fileCover->move($this->getParameter('uploads_directory').'/'.$currentId,$fileNameCover);
                 $restaurant->setCoverPath($fileNameCover);

             }
             if ($image1 != null) {
                 $fileNameImage1 = $this->generateUniqueFileName().'.'.$image1->guessExtension();
                 $image1->move($this->getParameter('uploads_directory').'/'.$currentId,$fileNameImage1);
                 $restaurant->setImage1($fileNameImage1);

             }
             if ($image2 != null) {
                 $fileNameImage2 = $this->generateUniqueFileName().'.'.$image2->guessExtension();
                 $image2->move($this->getParameter('uploads_directory').'/'.$currentId,$fileNameImage2);
                 $restaurant->setImage2($fileNameImage2);
             }
             if ($image3 != null) {
                 $fileNameImage3 = $this->generateUniqueFileName().'.'.$image3->guessExtension();
                 $image3->move($this->getParameter('uploads_directory').'/'.$currentId,$fileNameImage3);
                 $restaurant->setImage3($fileNameImage3);
             }
             if ($image4 != null) {
                 $fileNameImage4 = $this->generateUniqueFileName().'.'.$image4->guessExtension();
                 $image4->move($this->getParameter('uploads_directory').'/'.$currentId,$fileNameImage4);
                 $restaurant->setImage4($fileNameImage4);

             }

            $em->persist($restaurant);
            $em->flush();
            $this->addFlash('success', 'Your changes were saved!');
            return $this->redirectToRoute('restaurants');
        }
        return $this->render('restaurants/new.html.twig', [
            'form' => $form->createView(),
            'request' => $request->query->get('search')

        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_restaurant")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $restaurant = $em->getRepository('App:Restaurant')->find($id);
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);


        if (!$restaurant) {
            $this->addFlash('danger', 'Your Restaurant not exist');
            return $this->redirectToRoute('restaurants');
        }
        $fileCover = $restaurant->getCoverPath();
        $image1 = $restaurant->getImage1();
        $image2 = $restaurant->getImage2();
        $image3 = $restaurant->getImage3();
        $image4 = $restaurant->getImage4();


        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->files->get('restaurant')['coverPath'] != null) {
                $fileNameCover = $this->generateUniqueFileName().'.'.$fileCover->guessExtension();
                $fileCover->move($this->getParameter('uploads_directory').'/'.$id,$fileNameCover);
                $restaurant->setCoverPath($fileNameCover);
            }
            if ($request->files->get('restaurant')['image1'] != null) {
                $fileNameImage1 = $this->generateUniqueFileName().'.'.$image1->guessExtension();
                $image1->move($this->getParameter('uploads_directory').'/'.$id,$fileNameImage1);
                $restaurant->setImage1($fileNameImage1);

            }
            if ($request->files->get('restaurant')['image2'] != null) {
                $fileNameImage2 = $this->generateUniqueFileName().'.'.$image2->guessExtension();
                $image2->move($this->getParameter('uploads_directory').'/'.$id,$fileNameImage2);
                $restaurant->setImage2($fileNameImage2);
            }
            if ($request->files->get('restaurant')['image3'] != null) {
                $fileNameImage3 = $this->generateUniqueFileName().'.'.$image3->guessExtension();
                $image3->move($this->getParameter('uploads_directory').'/'.$id,$fileNameImage3);
                $restaurant->setImage3($fileNameImage3);
            }

            if ($request->files->get('restaurant')['image4'] != null) {
                $fileNameImage4 = $this->generateUniqueFileName().'.'.$image4->guessExtension();
                $image4->move($this->getParameter('uploads_directory').'/'.$id,$fileNameImage4);
                $restaurant->setImage4($fileNameImage4);

            }

            $em->flush();
            $this->addFlash('success', 'Your Restaurant has been edited');
            return $this->redirectToRoute('restaurants');
        }
        return $this->render('restaurants/edit.html.twig', [
            'form' => $form->createView()

        ]);

    }

    /**
     * @Route("/view/{id}", name="view_restaurant")
     */
    public function viewAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $restaurant = $em->getRepository('App:Restaurant')->find($id);
        $booking = $em->getRepository('App:Bookings')->findBy([
           'restaurantId' => $id
        ]);


        return $this->render('restaurants/view.html.twig', [
                'restaurant' => $restaurant,
                'booking' => $booking,
                'project_path' => $this->getParameter('project_path'),

            ]
        );
    }

    /**
     * @Route("/remove/{id}", name="remove_restaurant")
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $restaurant = $em->getRepository('App:Restaurant')->find($id);
        if (!$restaurant) {
            $this->addFlash('danger', 'Your Restaurant does not exist');
            return $this->redirectToRoute('restaurants');
        }
        $em->remove($restaurant);
        $em->flush();

        $this->addFlash('success', 'Your Restaurant has been deleted');
        return $this->redirectToRoute('restaurants');
    }


    /**
     * @Route("/user/myRestaurants", name="my_restaurants")
     */
    public function myRestaurantsAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Please login');
            return $this->redirectToRoute('restaurants');
        }

        $em = $this->getDoctrine()->getManager();
        $myRestaurant = $em->getRepository('App:Restaurant')->myRestaturant($user->getId());

        return $this->render('user/myRestaurants.html.twig', [
                'restaurant' => $myRestaurant,

            ]
        );

    }

    /**
     * @Route("/user/myRestaurant/{id}", name="my_restaurant")
     */
    public function myRestaurantAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $myRestaurant = $em->getRepository('App:Restaurant')->findBy([
            'id' => $id,
            'userId' => $this->getUser()
        ]);

        return $this->render('user/myView.html.twig', [
            'restaurant' => $myRestaurant,
        ]);
    }

    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

}
