<?php
class BaseappController extends Object
{
    public function main()
    {
        $render = $this->getTemplate("base_main");
        $manager = $this->getManager("baseapp");
        $account = $this->getManager("account")->getAccount();


        $window = $manager->getWindow();

        //$main_menu = $manager->getMainMenu();

        $status_bar = $manager->getStatusBar();

        $render->setWindow($window);
        //$render->setMainMenu($main_menu);
        $render->setStatusBar($status_bar);
        $render->setRoles($account->getRolesAsNameArray());
        #$render->setUsername($this->getManager('auth')->getAccountUsername());

        return $render;
    }
}
?>