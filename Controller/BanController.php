<?php

/*
 * This file is part of the CCDNUser AdminBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNUser\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use FOS\UserBundle\Model\UserInterface;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class BanController extends ContainerAware
{

    /**
     *
     * @access public
     * @param  int                             $page
     * @return RedirectResponse|RenderResponse
     */
    public function showBannedUsersAction($page)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $users_paginated = $this->container->get('ccdn_user_user.user.repository')->findAllBannedPaginated();

        $users_per_page = $this->container->getParameter('ccdn_user_admin.ban.show_banned_users.users_per_page');
        $users_paginated->setMaxPerPage($users_per_page);
        $users_paginated->setCurrentPage($page, false, true);

        $users = $users_paginated->getCurrentPageResults();

        $crumb_trail = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('crumbs.dashboard.admin', array(), 'CCDNUserAdminBundle'), $this->container->get('router')->generate('cc_dashboard_show', array('category' => 'admin')), "sitemap")
            ->add($this->container->get('translator')->trans('crumbs.show_banned', array(), 'CCDNUserAdminBundle'), $this->container->get('router')->generate('cc_admin_user_show_banned'), "users");

        return $this->container->get('templating')->renderResponse('CCDNUserAdminBundle:Ban:show_banned_users.html.' . $this->getEngine(), array(
            'crumbs' => $crumb_trail,
            'user_profile_route' => $this->container->getParameter('ccdn_user_admin.user.profile_route'),
            'pager' => $users_paginated,
            'users' => $users,
        ));
    }

    /**
     *
     * @access public
     * @param  int                             $user_id
     * @return RedirectResponse|RenderResponse
     */
    public function banUserAction($user_id)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $user = $this->container->get('ccdn_user_user.user.repository')->findOneById($user_id);

        if ( ! is_object($user) || ! $user instanceof UserInterface) {
            throw new NotFoundHttpException('the user does not exist.');
        }

        if ($user->getId() == $this->container->get('security.context')->getToken()->getUser()->getId()) {
            throw new AccessDeniedException('You cannot administrate yourself.');
        }

        $this->container->get('ccdn_user_user.user.manager')->ban($user)->flush();

        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.user.ban.success', array('%username%' => $user->getUsername()), 'CCDNUserAdminBundle'));

        return new RedirectResponse($this->container->get('router')->generate('cc_admin_user_show', array('user_id' => $user_id)));

    }

    /**
     *
     * @access public
     * @param  int                             $user_id
     * @return RedirectResponse|RenderResponse
     */
    public function unbanUserAction($user_id)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $user = $this->container->get('ccdn_user_user.user.repository')->findOneById($user_id);

        if ( ! is_object($user) || ! $user instanceof UserInterface) {
            throw new NotFoundHttpException('the user does not exist.');
        }

        if ($user->getId() == $this->container->get('security.context')->getToken()->getUser()->getId()) {
            throw new AccessDeniedException('You cannot administrate yourself.');
        }

        $this->container->get('ccdn_user_user.user.manager')->unban($user)->flush();

        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.user.unban.success', array('%username%' => $user->getUsername()), 'CCDNUserAdminBundle'));

        return new RedirectResponse($this->container->get('router')->generate('cc_admin_user_show', array('user_id' => $user_id)));

    }

    /**
     *
     * @access public
     * @return RedirectResponse|RenderResponse
     */
    public function banIPAction()
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

    }

    /**
     *
     * @access public
     * @return RedirectResponse|RenderResponse
     */
    public function banEMailAddressAction()
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

    }

    /**
     *
     * @access protected
     * @return string
     */
    protected function getEngine()
    {
        return $this->container->getParameter('ccdn_user_admin.template.engine');
    }

}
