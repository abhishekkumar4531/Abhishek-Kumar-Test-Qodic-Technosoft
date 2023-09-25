<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Home Controller
 * It is responsible for managing the response of all request like Home page, 
 * Registration, Login and Profile view request
 */
class HomeController extends AbstractController
{

	// Object of User class.
	public $user;

	// Object of EntityManagerInterface 
	public $entityManager;

	// Storing the repository of User class.
	public $userRepo;

	// Stores the user data when user send the request for profile request.
	public $userData;

	/**
   * __construct - It will initialize the class and store objects in $entityManager,
   * and $user.
   *
   *   @param  mixed $entityManager
   *     It is to manage persistance and retriveal Entity object from Database.
   *
   *   @return void
   */
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->user = new User();
		$this->entityManager = $entityManager;
		$this->userRepo = $entityManager->getRepository(User::class);
	}

	/**
	 * index - It is responsible for Home page related request, it will render to
	 * home page according to user role.
	 * 
	 * 		@param mixed $request
	 * 			This object is handing the request.
	 * 		@return Response
	 * 			When user request come without login then open a normal page.
	 * 			When user come as a User role then also show a normal page.
	 * 			When user come as manager then show a list of all user.
	 * 			When user come as Super-Admin then redirect to a page where admin can
	 * 			view, change the role, delete and add the user.
	 */
	#[Route('/home', name: 'app_home')]
	#[Route('/', name: 'app_home_default')]
	public function index(Request $request): Response
	{
		$session = $request->getSession();
		// If user logged in then execute if statement otherwise destroy the session.
		if ($session->get('userLoggedIn')) {
			$userDataList = $this->userRepo->findAll();
			$availableRoles = ["Super-Admin", "Manager", "User"];
			if ($session->get('userRole') === "Super-Admin") {
				return $this->render('home/admin.html.twig', [
					"userDataList" => $userDataList,
					"availableRoles" => $availableRoles,
					"userRole" => $session->get('userRole'),
					"loggedOpt" => "/profile",
					"loggedValue" => $session->get('loggedName'),
					"loginOut" => "/logout",
					"logValue" => "Logout"
				]);
			} elseif ($session->get('userRole') === "Manager") {
				return $this->render('home/manager.html.twig', [
					"userDataList" => $userDataList,
					"userRole" => $session->get('userRole'),
					"loggedOpt" => "/profile",
					"loggedValue" => $session->get('loggedName'),
					"loginOut" => "/logout",
					"logValue" => "Logout"
				]);
			} else {
				return $this->render('home/index.html.twig', [
					"loggedOpt" => "/profile",
					"loggedValue" => $session->get('loggedName'),
					"loginOut" => "/logout",
					"logValue" => "Logout"
				]);
			}
		} else {
			$session->invalidate();
			return $this->render('home/index.html.twig', [
				"loggedOpt" => "/register",
				"loggedValue" => "Register",
				"loginOut" => "/login",
				"logValue" => "Login"
			]);
		}
	}

	/**
	 * Login - It is responsible for Login of an User
	 * 
	 * 		@param mixed $request
	 * 			This object is handing the request.
	 * 		@return Response
	 * 			When request come for login then redirect to login page.
	 * 			When user submit a login page then first validate the data if valid then
	 *      redirect to home page.
	 */
	#[Route('/login', name: 'app_login')]
	public function login(Request $request): Response
	{
		if (isset($_POST['loginButton'])) {
			$loginForm = $request->request->all();
			if ($loginForm != NULL) {
				$userEmail = $loginForm["userEmail"];
				$userPassword = $loginForm["userPassword"];
				$fetchCredentials = $this->userRepo->findOneBy(['userEmail' => $userEmail]);
				if ($fetchCredentials) {
					$validatePassword = $fetchCredentials->getUserPassword();
					if ($validatePassword === $userPassword) {
						$session = $request->getSession();
						$session->set('userLoggedIn', $userEmail);
						$session->set('loggedName', $fetchCredentials->getFirstName());
						$session->set('userRole', $fetchCredentials->getUserRole());
						return $this->redirectToRoute('app_home');
					} else {
						return $this->render('home/login.html.twig', [
							'userValue' => $userEmail,
							'userPassword' => $userPassword,
							'invalidPassword' => "Please enter valid password",
							"loggedOpt" => "/register",
							"loggedValue" => "Register",
							"loginOut" => "/login",
							"logValue" => "Login"
						]);
					}
				} else {
					return $this->render('home/login.html.twig', [
						'userValue' => $userEmail,
						'userPassword' => $userPassword,
						'invalidValue' => "Please enter valid email",
						"loggedOpt" => "/register",
						"loggedValue" => "Register",
						"loginOut" => "/login",
						"logValue" => "Login"
					]);
				}
			}
		} else {
			return $this->render('home/login.html.twig', [
				"loggedOpt" => "/register",
				"loggedValue" => "Register",
				"loginOut" => "/login",
				"logValue" => "Login"
			]);
		}
	}

	/**
	 * Register - It is responsible for Register of an User
	 * 
	 * 		@param mixed $request
	 * 			This object is handing the request.
	 * 		@return Response
	 * 			When request come for registration then redirect to Registration page.
	 * 			When user submit the registration page then first validate the data if valid then
	 *      registered the user and redirect to login page.
	 */
	#[Route('/register', name: 'app_register')]
	public function register(Request $request): Response
	{
		if (isset($_POST['registrationButton'])) {
			$registrationForm = $request->request->all();

			if ($registrationForm) {
				$imgName = $_FILES['userImage']['name'];
				$imgTmp = $_FILES['userImage']['tmp_name'];
				$imgType = $_FILES['userImage']['type'];
				if ($imgType == "image/png" || $imgType == "image/jpeg" || $imgType == "image/jpg") {
					move_uploaded_file($imgTmp, "assets/images/" . $imgName);
					$targetImage = "assets/images/" . $imgName;
				}
				$this->user->setFirstName($registrationForm['firstName']);
				$this->user->setLastName($registrationForm['lastName']);
				$this->user->setUserEmail($registrationForm['userEmail']);
				$this->user->setUserPhone($registrationForm['userPhone']);
				$this->user->setUserPassword($registrationForm['userPassword']);
				$this->user->setUserImage($targetImage);
				$this->user->setUserRole("User");

				$this->entityManager->persist($this->user);
				$this->entityManager->flush();

				return $this->redirectToRoute('app_login');
			}
		} else {
			return $this->render('home/register.html.twig', [
				"loggedOpt" => "/register",
				"loggedValue" => "Register",
				"loginOut" => "/login",
				"logValue" => "Login"
			]);
		}
	}

	/**
	 * Profile - It is responsible for Profile view of an User.
	 * 
	 * 		@param mixed $request
	 * 			This object is handing the request.
	 * 		@return Response
	 * 			When request come for profile view then redirect to profile page.
	 * 			When user update their data then also update and again redirect to profile page.
	 */
	#[Route('/profile', name: 'app_profile')]
	public function profile(Request $request): Response
	{
		$session = $request->getSession();
		if ($session->get('userLoggedIn')) {
			if (isset($_POST['updateButton'])) {
				$updateForm = $request->request->all();
				if ($updateForm) {
					$fetchData = $this->userRepo->findOneBy(['userEmail' => $updateForm['userEmail']]);
					if (!empty($_FILES['userImage']['name'])) {
						$imgName = $_FILES['userImage']['name'];
						$imgTmp = $_FILES['userImage']['tmp_name'];
						$imgType = $_FILES['userImage']['type'];
						if ($imgType == "image/png" || $imgType == "image/jpeg" || $imgType == "image/jpg") {
							move_uploaded_file($imgTmp, "assets/images/" . $imgName);
							$targetImage = "assets/images/" . $imgName;
						}
						$fetchData->setUserImage($targetImage);
					}
					$fetchData->setFirstName($updateForm['firstName']);
					$fetchData->setLastName($updateForm['lastName']);
					$fetchData->setUserEmail($updateForm['userEmail']);
					$fetchData->setUserPhone($updateForm['userPhone']);

					$this->entityManager->flush();

					return $this->redirectToRoute('app_profile');
				}
			} else {
				$userEmail = $session->get('userLoggedIn');
				$fetchCredentials = $this->userRepo->findOneBy(['userEmail' => $userEmail]);
				if ($fetchCredentials) {
					$this->userData['firstName'] = $fetchCredentials->getFirstName();
					$this->userData['lastName'] = $fetchCredentials->getLastName();
					$this->userData['userEmail'] = $fetchCredentials->getUserEmail();
					$this->userData['userPhone'] = $fetchCredentials->getUserPhone();
					$this->userData['userImage'] = $fetchCredentials->getUserImage();

					return $this->render('home/profile.html.twig', [
						"userData" => $this->userData,
						"loggedOpt" => "/profile",
						"loggedValue" => $session->get('loggedName'),
						"loginOut" => "/logout",
						"logValue" => "Logout"
					]);
				}
			}
		} else {
			$session->invalidate();
			return $this->redirectToRoute('app_login');
		}
	}

	/**
	 * adminUpdate - It is responsible for when admin try to update user role
	 * 
	 * 		@param mixed $request
	 * 			This object is handing the request.
	 * 		@return Response
	 * 			When updated successfully then return true with JSON format otherwise
	 * 			false with JSON format.
	 */
	#[Route('/adminUpdate', name: 'app_admin_update')]
	public function adminUpdate(Request $request): Response
	{
		$session = $request->getSession();
		if ($session->get('userLoggedIn')) {
			if (true) {
				$userId = $_POST['userId'];
				$newRole = $_POST['newRole'];
				$fetchData = $this->userRepo->findOneBy(['id' => $userId]);

				if ($fetchData) {
					$fetchData->setUserRole($newRole);

					$this->entityManager->flush();
					return $this->json(['status' => TRUE]);
				}
			} else {
				return $this->redirectToRoute("app_home_default");
			}
		} else {
			$session->invalidate();
			return $this->redirectToRoute("app_login");
		}
	}

	/**
	 * deleteUser - It is responsible for when admin try to delete an user.
	 * 
	 * 		@param mixed $request
	 * 			This object is handing the request.
	 * 		@return Response
	 * 			When deleted successfully then return true with JSON format otherwise
	 * 			false with JSON format.
	 */
	#[Route('/deleteUser', name: 'app_delete_user')]
	public function deleteUser(Request $request): Response
	{
		$session = $request->getSession();
		if ($session->get('userLoggedIn')) {
			if (true) {
				$userId = $_POST['userId'];
				$fetchData = $this->userRepo->findOneBy(['id' => $userId]);

				if ($fetchData) {
					$this->entityManager->remove($fetchData);

					$this->entityManager->flush();
					return $this->json(['status' => TRUE]);
				}
			} else {
				return $this->redirectToRoute("app_home_default");
			}
		} else {
			$session->invalidate();
			return $this->redirectToRoute("app_login");
		}
	}

	/**
	 * addUser - It is responsible for when admin try to add a new user.
	 * 
	 * 		@param mixed $request
	 * 			This object is handing the request.
	 * 		@return Response
	 * 			When user added successfully then return true with JSON format otherwise
	 * 			false with JSON format.
	 */
	#[Route('/addUser', name: 'app_add_user')]
	public function addUser(Request $request): Response
	{
		if (isset($_POST['addButton'])) {
			$addNew = $request->request->all();

			if ($addNew) {
				$imgName = $_FILES['userImage']['name'];
				$imgTmp = $_FILES['userImage']['tmp_name'];
				$imgType = $_FILES['userImage']['type'];
				if ($imgType == "image/png" || $imgType == "image/jpeg" || $imgType == "image/jpg") {
					move_uploaded_file($imgTmp, "assets/images/" . $imgName);
					$targetImage = "assets/images/" . $imgName;
				}
				$this->user->setFirstName($addNew['firstName']);
				$this->user->setLastName($addNew['lastName']);
				$this->user->setUserEmail($addNew['userEmail']);
				$this->user->setUserPhone($addNew['userPhone']);
				$this->user->setUserPassword($addNew['userPassword']);
				$this->user->setUserImage($targetImage);
				$this->user->setUserRole($addNew['userRole']);

				$this->entityManager->persist($this->user);
				$this->entityManager->flush();

				return $this->redirectToRoute('app_home_default');
			}
		}
	}

	/**
	 * logout - It is responsible for destroy all the session and marked user as Logout.
	 * 
	 * 		@param mixed $request
	 * 			This object is handing the request.
	 * 		@return Response
	 * 			If user not logged in the simply rediret to login page.
	 * 			If user logged in then first destroyed the session and then redirect
	 * 			to Home page.
	 */
	#[Route('/logout', name: 'app_logout')]
	public function logout(Request $request): Response
	{
		$session = $request->getSession();
		if ($session->get('userLoggedIn')) {
			$session->invalidate();
			return $this->redirectToRoute("app_home_default");
		} else {
			$session->invalidate();
			return $this->redirectToRoute("app_login");
		}
	}
}
