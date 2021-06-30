<?php

final class MessageView {

	private $loc;
	private $input;
	private $modules;
	private $errors;
	private $messages;

	public function __construct($loc = array(), $input = array(), $modules = array(), $errors = array(), $messages = array()) {

		$this->loc = $loc;
		$this->input = $input;
		$this->modules = $modules;
		$this->errors = $errors;
		$this->messages = $messages;

	}

	public function messageInbox(MessageInboxParameters $arg) {

		$body = '

			<div class="row">
				<div class="col-12 col-sm-6 offset-sm-6 col-md-3 offset-md-9 col-lg-2 offset-lg-10">
					<a href="/' . Lang::prefix() . 'message/draft/" class="btn btn-block btn-outline-success">' . Lang::getLang('messageDraft') . '</a>
				</div>
			</div>

			<div class="table-container mt-2">

				<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover table-sm">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="text-center">' . Lang::getLang('id') . '</th>
								<th scope="col" class="text-center">' . Lang::getLang('messageFlag') . '</th>
								<th scope="col" class="text-center">' . Lang::getLang('messageReadState') . '</th>
								<th scope="col" class="text-center">' . Lang::getLang('messageSubject') . '</th>
								<th scope="col" class="text-center">' . Lang::getLang('action') . '</th>
							</tr>
						</thead>
						<tbody>' . $this->messageInboxList($arg) . '</tbody>
					</table>
				</div>
			</div>

		';

		$breadcrumbs = $this->messageBreadcrumbs('inbox');
		$header = Lang::getLang('messageInbox');
		$card = new CardView('message_inbox',array('container'),$breadcrumbs,array('col-12'),$header,$body);
		return $card->card();

	}
	
	public function messageInboxList(MessageInboxParameters $arg) {

		$inbox = new MessageInbox($arg);
		$messages = $inbox->messages();

		$messages = array(1,2,3); // temp

		$rows = '';

		foreach ($messages AS $messageID) {

			// $message = new Message($messageID);

			$rows .= '
				<tr id="message_id_' . $messageID . '" class="inbox-message-row">
					<th scope="row" class="text-center">' . $messageID . '</th>
					<td class="text-center">messageFlag</td>
					<td class="text-center">messageReadState</td>
					<td class="text-left">messageSubject</td>
					<td class="text-center text-nowrap">
						<a href="/' . Lang::prefix() . 'message/read/' . $messageID . '/" class="btn btn-sm btn-outline-info">' . Lang::getLang('messageRead') . '</a>
						<a href="/' . Lang::prefix() . 'message/confirm-delete/' . $messageID . '/" class="btn btn-sm btn-outline-danger">' . Lang::getLang('delete') . '</a>
					</td>
				</tr>
			';

		}

		return $rows;


	}
	
	public function messageDraft() {

		$form = 'MESSAGE DRAFT';

		$header = Lang::getLang('messageDraft');
		$breadcrumbs = $this->messageBreadcrumbs('draft');
		$card = new CardView('message_read',array('container'),$breadcrumbs,array('col-12'),$header,$form);
		return $card->card();

	}

	public function messageRead($messageID) {

		$read = 'MESSAGE READ ' . $messageID;

		$header = Lang::getLang('messageView');
		$breadcrumbs = $this->messageBreadcrumbs('read', $messageID);
		$card = new CardView('message_read',array('container'),$breadcrumbs,array('col-12'),$header,$read);
		return $card->card();

	}

	public function messageConfirmDelete($messageID) {

		$confirm = 'MESSAGE CONFIRM DELETE ' . $messageID;

		$header = Lang::getLang('messageConfirmDelete');
		$breadcrumbs = $this->messageBreadcrumbs('confirm-delete', $messageID);
		$card = new CardView('message_confirm_delete',array('container'),$breadcrumbs,array('col-12'),$header,$confirm);
		return $card->card();

	}

	private function messageBreadcrumbs($page, $messageID = null) {

		if (!in_array($page,array('inbox','draft','read','confirm-delete'))) { die('perihelion encountered a problem with messageBreadcrumbs()'); }

		$panko = '<nav aria-label="breadcrumb">';
			$panko .= '<ol class="breadcrumb">';

				$panko .= '<li class="breadcrumb-item' . ($page=='inbox'?' active':'') . '">';
					if ($page=='inbox') { $panko .= Lang::getLang('messageInbox'); }
					else { $panko .= '<a href="/' . Lang::prefix() . 'message/">' . Lang::getLang('messageInbox') . '</a>'; }
				$panko .= '</li>';

				if ($page == 'draft') {
					$panko .= '<li class="breadcrumb-item active">' . Lang::getLang('messageDraft') . '</li>';
				}

				if (in_array($page,array('read','confirm-delete'))) {
					$panko .= '<li class="breadcrumb-item' . ($page=='read'?' active':'') . '">';
						if ($page=='read') { $panko .= Lang::getLang('message'); }
						if ($page=='confirm-delete') { $panko .= '<a href="/' . Lang::prefix() . 'message/read/' . $messageID . '/">' . $mailSubject . '</a>'; }
					$panko .= '</li>';
				}

				if ($page == 'confirm-delete') {
					$panko .= '<li class="breadcrumb-item active">' . Lang::getLang('mailConfirmDelete') . '</li>';
				}


			$panko .= '</ol>';
		$panko .= '</nav>';

		return $panko;

	}

}

?>