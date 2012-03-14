<?php

namespace Coregen\AdminGeneratorBundle\ORM;

use Coregen\AdminGeneratorBundle\Controller;
use Coregen\AdminGeneratorBundle\Generator\Generator;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;

/**
 * ORM Generator Controller
 *
 * @package    CoregenAdminGenerator
 * @subpackage ORM
 * @author     Rafael Goulart <rafaelgou@gmail.com>
 */
abstract class GeneratorController extends Controller\GeneratorController
{
    /**
     * Load Generator
     *
     * @param Coregen\AdminGeneratorBundle\Generator\Generator $generator A generator
     *
     * @return void
     */
    public function loadGenerator($generator)
    {
        $this->generator = $generator;
        $this->pager = $this->get('coregen.orm.pager')
                ->setGenerator($generator);
    }

    /**
     * Index action
     *
     * @return View
     */
    public function indexAction()
    {
        // Configuring the Generator Controller
        $this->configure();

        // Defining filters
        $this->configureFilter();

        // Defining sort
        $this->configureSort();

        // Defining actual page
        $this->setPage($this->getRequest()->get('page', $this->getPage()));

        $pager = $this->getPager();

        $filterForm = $this->createFilterForm();
        if ($filterForm) {
            $filterForm = $filterForm->createView();
        }
        return $this->renderView('list' . ucfirst($this->generator->list->layout), array(
            'pager'      => $pager,
            'delete_form' => $this->createDeleteForm('fake')->createView(),
            'filter_form' => $filterForm,
        ));

    }

    /**
     * Show action
     *
     * @param mixed $id Entity/Document Id
     *
     * @return View
     */
    public function showAction($id)
    {
        // Configuring the Generator Controller
        $this->configure();

        $manager = $this->getDoctrine()->getEntityManager();

        $entity = $manager->getRepository($this->generator->model)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->renderView('show', array(
            'record'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * New action
     *
     * @return View
     */
    public function newAction()
    {
        // Configuring the Generator Controller
        $this->configure();

        $entityClass = $this->generator->class;
        $formType = $this->generator->form->type;

        $entity = new $entityClass();
        $form   = $this->createForm(new $formType(), $entity);

        return $this->renderView('new', array(
            'record' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Create Action
     *
     * @return View
     */
    public function createAction()
    {
        // Configuring the Generator Controller
        $this->configure();

        $entityClass = $this->generator->class;
        $formType = $this->generator->form->type;

        $entity = new $entityClass();
        $form   = $this->createForm(new $formType(), $entity);

        $request = $this->getRequest();
        $form->bindRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $manager->persist($entity);
            $manager->flush();

            if ($request->get('form_save_and_add') == 'true') {
                $this->get('session')->setFlash('success', 'The item was created successfully. Add a new one bellow.');
                return $this->redirect($this->generateUrl($this->generator->route . '_new'));
            } else {
                $this->get('session')->setFlash('success', 'The item was created successfully.');
                return $this->redirect($this->generateUrl($this->generator->route));
                //return $this->redirect($this->generateUrl($this->generator->route . '_show', array('id' => $entity->getId())));
            }

        }

        $this->get('session')->setFlash('error', 'An error ocurred while saving the item. Check the informed data.');
        return $this->renderView('new', array(
            'record' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Edit Action
     *
     * @param mixed $id Entity/Document Id
     *
     * @return View
     */
    public function editAction($id)
    {
        // Configuring the Generator Controller
        $this->configure();

        $manager = $this->getDoctrine()->getEntityManager();

        $entity = $manager->getRepository($this->generator->model)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity.');
        }

        $formType = $this->generator->form->type;

        $editForm = $this->createForm(new $formType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        foreach ($this->generator->getHiddenFields('edit') as $fieldName) {
            $editForm->remove($fieldName);
        }

        return $this->renderView('edit', array(
            'record'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Update Action
     *
     * @param mixed $id Entity/Document Id
     *
     * @return View
     */
    public function updateAction($id)
    {
        // Configuring the Generator Controller
        $this->configure();

        $manager = $this->getDoctrine()->getEntityManager();

        $entity = $manager->getRepository($this->generator->model)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }

        $formType = $this->generator->form->type;

        $editForm = $this->createForm(new $formType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        foreach ($this->generator->getHiddenFields('edit') as $fieldName) {
            $editForm->remove($fieldName);
        }
        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $manager->persist($entity);
            $manager->flush();

            $this->get('session')->setFlash('success', 'The item was updated successfully.');
            return $this->redirect($this->generateUrl($this->generator->route));
            //return $this->redirect($this->generateUrl($this->generator->route . '_show', array('id' => $id)));
        } else {
            $this->get('session')->setFlash('error', 'An error ocurred while saving the item. Check the informed data.');
            return $this->renderView('edit', array(
                'record'      => $entity,
                'form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));

        }

    }

    /**
     * Delete Action
     *
     * @param mixed $id Entity/Document Id
     *
     * @return View
     */
    public function deleteAction($id)
    {
        // Configuring the Generator Controller
        $this->configure();

        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getEntityManager();
            $entity = $manager->getRepository($this->generator->class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find entity.');
            }

            $manager->remove($entity);
            $manager->flush();

            $this->get('session')->setFlash('success', 'The item was deleted successfully.');
            return $this->redirect($this->generateUrl($this->generator->route));
        } else {
            $this->get('session')->setFlash(
                'error',
                'An error ocurred while deleting the item.'
            );
            return $this->redirect($this->generateUrl($this->generator->route));
        }

    }

    /**
     * Batch Delete
     *
     * @return View
     */
    public function batchActionsAction()
    {
        // Configuring the Generator Controller
        $this->configure();

        $method = 'batch' . ucfirst($this->getRequest()->get('batchSelect', 'none'));
        if (method_exists($this, $method)) {
            $ids = array_keys($this->getRequest()->get('selected_objects', array()));
            return $this->$method($ids);
        } else {
            $message = $this->get('translator')->trans('Method "%s" not found in class "%s".');
            $message = sprintf($message, $method, get_class($this));
            $this->get('session')->setFlash('error',$message);
            return $this->redirect($this->generateUrl($this->generator->route));
        }
    }

    /**
     * Batch Delete
     *
     * @param array $ids An array of ids to batch process
     * @return View
     */
    protected function batchDelete($ids)
    {
        // Configuring the Generator Controller
        $this->configure();

        if (count($ids)) {

            try {
                $manager = $this->getDoctrine()->getEntityManager();
                $qb = $manager->createQueryBuilder($this->generator->class, 'e');
                $qb->delete($this->generator->class, 'e')
                    ->andWhere($qb->expr()->in('e.id', $ids))
                    ->getQuery()->execute();
            } catch (Exception $exc) {

                //echo $exc->getTraceAsString();
                $this->get('session')->setFlash(
                        'error',
                        'An error ocurred while executing the batch action.'
                        . '<br/>' . $exc->getTraceAsString()
                        );
                return $this->redirect($this->generateUrl($this->generator->route));
            }

            $batch_actions = $this->generator->list->batch_actions;
            $action = $batch_actions['delete'];

            $message = $this->get('translator')->trans($action['success_message']);
            $message = sprintf($message, count($ids));

            $this->get('session')->setFlash('success', $message);
            return $this->redirect($this->generateUrl($this->generator->route));

        } else {
            $this->get('session')->setFlash('error','No itens selected to execute the batch action.');
            return $this->redirect($this->generateUrl($this->generator->route));
        }

    }

}
