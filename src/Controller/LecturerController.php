<?php

namespace App\Controller;

use App\Entity\Lecturer;
use App\Form\LecturerType;
use App\Repository\LecturerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use function PHPUnit\Framework\throwException;

/**
 * @IsGranted("ROLE_MANAGER")
 */
#[Route('/lecturer')]
class LecturerController extends AbstractController
{
    #[Route('/ ', name: 'view_lecturer_list')]
    public function LecturerIndex(LecturerRepository $lecturerRepository) {
        $lecturers = $lecturerRepository->viewAllLecturer();
        return $this->render("lecturer/index.html.twig",
        [
            'lecturers' => $lecturers
        ]);
    }

    #[Route('/detail/{id}', name: 'view_lecturer_by_id')]
    public function LecturerDetail(ManagerRegistry $managerRegistry, $id) {
        $lecturer = $managerRegistry->getRepository(Lecturer::class)->find($id);
        return $this->render("lecturer/detail.html.twig",
        [
            'lecturer' => $lecturer
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_lecturer')]
    public function LecturerDelete(ManagerRegistry $managerRegistry, $id) {
        $lecturer = $managerRegistry->getRepository(Lecturer::class)->find($id);
        if ($lecturer == null) {
            $this->addFlash("Error","Lecturer not found !");        
        } 
        else if (count($lecturer->getCourse()) >= 1 ) {
            $this->addFlash("Error","Can not delete this lecturer !");
        }
        else {
            $manager = $managerRegistry->getManager();
            $manager->remove($lecturer);
            $manager->flush();
            $this->addFlash("Success","Delete lecturer succeed  !");
        }
        return $this->redirectToRoute("view_lecturer_list");
    }

    #[Route('/add', name: 'add_lecturer')]
    public function LecturerAdd(Request $request) {
        $lecturer = new Lecturer;
        $form = $this->createForm(LecturerType::class,$lecturer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //code x??? l?? vi???c upload ???nh
            //B1: t???o 1 bi???n ????? l???y d??? li???u ???nh ???????c upload t??? form
            $image = $lecturer->getImage();
            //B2: t???o t??n m???i cho ???nh => ?????m b???o t??n ???nh l?? duy nh???t
            $imgName = uniqid(); //unique id
            //B3: l???y ??u??i (extension) c???a file ???nh
            //Note: c???n x??a data type "string" trong getter & setter c???a file Entity
            $imgExtension = $image->guessExtension();
            //B4: t???o t??n file ho??n thi???n cho ???nh (t??n m???i + ??u??i c??)
            $imageName = $imgName . "." . $imgExtension;
            //B5: di chuy???n file ???nh ?????n th?? m???c ch??? ?????nh ??? trong project  
            //Note1: c???n t???o th?? m???c ch???a ???nh trong public
            //Note2: c???u h??nh parameter trong file services.yaml (th?? m???c config)
             try {
                $image->move (
                    $this->getParameter('lecturer_image'),$imageName
                );
            } catch (FileException $e) {
                throwException($e);
            }
            //B6: l??u t??n ???nh v??o trong DB
            $lecturer->setImage($imageName);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($lecturer);
            $manager->flush();
            $this->addFlash("Success","Add lecturer succeed !");
            return $this->redirectToRoute("view_lecturer_list");
        }
        return $this->renderForm("lecturer/add.html.twig",
        [
            'lecturerForm' => $form
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_lecturer')]
    public function LecturerEdit(Request $request, ManagerRegistry $managerRegistry, $id) {
        $lecturer = $managerRegistry->getRepository(Lecturer::class)->find($id);
        if ($lecturer == null) {
            $this->addFlash("Error","Lecturer not found !");
            return $this->redirectToRoute("view_lecturer_list");        
        } else {
            $form = $this->createForm(LecturerType::class,$lecturer);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                //ki???m tra xem ng?????i d??ng c?? mu???n upload ???nh m???i hay kh??ng
                //n???u c?? th?? th???c hi???n code upload ???nh
                //n???u kh??ng th?? b??? qua
                $imageFile = $form['image']->getData();
                if ($imageFile != null) {
                    //B1: t???o 1 bi???n ????? l???y d??? li???u ???nh ???????c upload t??? form
                    $image = $lecturer->getImage();
                    //B2: t???o t??n m???i cho ???nh => ?????m b???o t??n ???nh l?? duy nh???t
                    $imgName = uniqid(); //unique id
                    //B3: l???y ??u??i (extension) c???a file ???nh
                    //Note: c???n x??a data type "string" trong getter & setter c???a file Entity
                    $imgExtension = $image->guessExtension();
                    //B4: t???o t??n file ho??n thi???n cho ???nh (t??n m???i + ??u??i c??)
                    $imageName = $imgName . "." . $imgExtension;
                    //B5: di chuy???n file ???nh ?????n th?? m???c ch??? ?????nh ??? trong project
                    //Note1: c???n t???o th?? m???c ch???a ???nh trong public
                    //Note2: c???u h??nh parameter trong file services.yaml (th?? m???c config)
                    try {
                        $image->move(
                            $this->getParameter('lecturer_image'),
                            $imageName
                        );
                    } catch (FileException $e) {
                        throwException($e);
                    }
                    //B6: l??u t??n ???nh v??o trong DB
                    $lecturer->setImage($imageName);
                }
                $manager = $managerRegistry->getManager();
                $manager->persist($lecturer);
                $manager->flush();
                $this->addFlash("Success","Edit lecturer succeed !");
                return $this->redirectToRoute("view_lecturer_list");
            }
            return $this->renderForm("lecturer/edit.html.twig",
            [
                'lecturerForm' => $form
            ]);
        }   
    }
}