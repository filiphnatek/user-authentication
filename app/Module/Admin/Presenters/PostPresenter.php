<?php
namespace App\Module\Admin\Presenters;

use Nette;
use App\Model\PostFacade; // Ensure correct namespace capitalization
use Nette\Application\UI\Form;

final class PostPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private PostFacade $facade, // Using PHP 8 promoted properties
    ) {}

    public function renderShow(int $postId): void
    {
        $post = $this->facade->getPostById($postId);
        if (!$post) {
            $this->error('Stránka nebyla nalezena'); // Proper error handling
        }

        $this->template->post = $post;
    }

    protected function createComponentCommentForm(): Form
    {
        $form = new Form;

        $form->addText('name', 'Jméno:')
             ->setRequired();

        $form->addEmail('email', 'E-mail:');

        $form->addTextArea('content', 'Komentář:')
             ->setRequired();

        $form->addSubmit('send', 'Publikovat komentář');
        $form->onSuccess[] = fn(Form $form, \stdClass $values) => $this->commentFormSucceeded($values);
        return $form;
    }

    private function commentFormSucceeded(\stdClass $data): void
    {
        $postId = $this->getParameter('postId');
        $this->flashMessage('Děkuji za komentář', 'success');
        $this->redirect('this');
    }

    public function actionShow(int $postId)
    {
        $post = $this->facade->getPostById($postId);

        if (!$this->getUser()->isLoggedIn() && $post->status == "ARCHIVED") {
            $this->flashMessage('Nemáš právo vidět archived, kámo!');
            $this->redirect('Home:default');
        }
    }

    public function handleLiked(int $postId, int $liked)
    {
        if ($this->getUser()->isLoggedIn()) {
            $userId = $this->getUser()->getId();
            $this->facade->updateRating($userId, $postId, $liked);
        } else {
            $this->flashMessage('Pro hodnocení příspěvků musíte být přihlášeni.', 'warning');
        }
    }
}
