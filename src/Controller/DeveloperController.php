<?php

namespace App\Controller;

use App\Entity\Developer;
use App\Form\DeveloperType;
use App\Repository\DeveloperRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/developer")
 */
class DeveloperController extends AbstractController
{
    /**
     * @Route("/", name="app_developer_index", methods={"GET"})
     */
    public function index(DeveloperRepository $developerRepository): Response
    {
        return $this->render('developer/index.html.twig', [
            'developers' => $developerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_developer_new", methods={"GET", "POST"})
     */
    public function new(Request $request, DeveloperRepository $developerRepository, SluggerInterface $slugger): Response
    {
        $developer = new Developer();
        $form = $this->createForm(DeveloperType::class, $developer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $File = $form['File']->getData();
            $originalFilename = pathinfo($File->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$File->guessExtension();
            $File->move($this->getParameter('directory'),$newFilename);
            $developer->setFile($newFilename);
            $developerRepository->add($developer, true);

            return $this->redirectToRoute('app_developer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('developer/new.html.twig', [
            'developer' => $developer,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_developer_show", methods={"GET"})
     */
    public function show(Developer $developer): Response
    {
        return $this->render('developer/show.html.twig', [
            'developer' => $developer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_developer_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Developer $developer, DeveloperRepository $developerRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(DeveloperType::class, $developer);
        $form->handleRequest($request);
        
        $img =$developer->getFile();
        $a=5;

        if($img !== null){
            $a=3;
            $developer->setFile( 
            new File($this->getParameter('directory').'/'.$developer->getFile()));
        }
        //dd($a);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $File = $form['File']->getData();
            //dd($File);
            /* working version */
            if($File !== null ){
                //$File = $form['File']->getData();
                //$File=$developer->getFile();    
                $originalFilename = pathinfo($File->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$File->guessExtension();
                $File->move($this->getParameter('directory'),$newFilename);
                $developer->setFile($newFilename);
            } else {
                $developer->setFile($img);
            }
            
            /*$developer->setFile(
            new File($this->getParameter('directory').'/'.$developer->getFile())
            );*/ 
            /*
            if($developer->getFile()!=null){
                $newFile = $developer->getFile();
                $$newFilename = uniqid().'.'.$newFile ->guessExtension();
                $File->move($this->getParameter('directory'),$newFilename);
                $developer->setFile($newFileName); 
            }*/

            $developerRepository->add($developer, true);
            return $this->redirectToRoute('app_developer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('developer/edit.html.twig', [
            'developer' => $developer,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_developer_delete", methods={"POST"})
     */
    public function delete(Request $request, Developer $developer, DeveloperRepository $developerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$developer->getId(), $request->request->get('_token'))) {
            $developerRepository->remove($developer, true);
        }

        return $this->redirectToRoute('app_developer_index', [], Response::HTTP_SEE_OTHER);
    }
}
