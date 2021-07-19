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
								<!--<th scope="col" class="text-center">' . Lang::getLang('id') . '</th>-->
								<th scope="col" class="text-center">' . Lang::getLang('messageFlag') . '</th>
								<th scope="col" class="text-center">' . Lang::getLang('messageSubject') . '</th>
								<th scope="col" class="text-center">' . Lang::getLang('messageCorrespondents') . '</th>
								<th scope="col" class="text-center">' . Lang::getLang('messageSendDateTime') . '</th>
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

		$rows = '';

		foreach ($messages AS $row) {

			$messageID = $row['messageID'];
			$message = new Message($messageID);
			$participants = $message->getParticipants();

			$correspondents = array();
			foreach ($participants AS $participant) {
				$correspondents[] = $participant['userDisplayName'];
			}
			$messageCorrespondents = implode(', ', $correspondents);

			$flagClasses = array('inbox-message-flag', 'far', 'fa-star');
			if ($row['flag'] == 'starred') { $flagClasses = array('inbox-message-flag', 'fas', 'fa-star'); }
			$flag = '<span class="' . implode(' ',$flagClasses) . '" data-participant-id="' . $_SESSION['userID'] . '" data-message-id="' . $messageID . '"></span>';

			$deleteButton = '';
			if ($message->creator == $_SESSION['userID']) {
				$deleteButton = '<a href="/' . Lang::prefix() . 'message/confirm-delete/' . $messageID . '/" class="btn btn-sm btn-outline-danger">' . Lang::getLang('delete') . '</a>';
			}

			$rows .= '
				<tr id="message_id_' . $messageID . '" class="inbox-message-row' . ($row['readState']=='unopened'?' table-success':'') . '">
					<!--<th scope="row" class="text-center">' . $messageID . '</th>-->
					<td class="text-center">' . $flag. '</td>
					<td class="text-left">' . $row['messageSubject']. '</td>
					<td class="text-left">' . $messageCorrespondents . '</td>
					<td class="text-center">' . $row['messageSendDateTime'] . '</td>
					<td class="text-center text-nowrap">
						<a href="/' . Lang::prefix() . 'message/read/' . $messageID . '/" class="btn btn-sm btn-outline-info">' . Lang::getLang('view') . '</a>
						' . $deleteButton . '
					</td>
				</tr>
			';

		}

		return $rows;


	}
	
	public function messageDraft() {

		$form = '
		
		
			<form method="post" action="/' . Lang::prefix() . 'message/draft/">
			
				<div class="form-row">
					<div class="form-group col-12 col-sm-8 col-md-4">
						<label for="participantUsers">' . Lang::getLang('messageTo') . '</label>
						' . UserView::userDropdown(0) . '
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-12 col-md-8">
						<label for="messageSubject">' . Lang::getLang('messageSubject') . '</label>
						<input type="text" class="form-control" name="messageSubject" placeholder="">
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-12">
						<label for="messageContent">' . Lang::getLang('messagesContent') . '</label>
						<textarea class="form-control" name="messageContent" placeholder="" rows="10"></textarea>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-12 col-sm-6 offset-sm-6 col-md-4 offset-md-8 col-lg-3 offset-lg-9 col-xl-2 offset-xl-10">
						<button type="submit" name="message-draft-submit" class="btn btn-primary btn-block">' . Lang::getLang('sendMessage') . '</button>
					</div>
				</div>
			
			</form>

		';

		$header = Lang::getLang('messageDraft');
		$breadcrumbs = $this->messageBreadcrumbs('draft');
		$card = new CardView('message_read',array('container'),$breadcrumbs,array('col-12'),$header,$form);
		return $card->card();

	}

	public function messageRead($messageID) {

		$message = new Message($messageID);
		$author = new User($message->creator);
		$header = $message->messageSubject;
		$content = htmlspecialchars($message->messageContent, ENT_QUOTES, 'UTF-8');

		$messageContent = '
			<div class="row font-weight-lighter">
				<div class="col-12 col-md-8">'. $author->getUserDisplayName() . '</div>
				<div class="col-12 col-md-4 text-md-right">'. $message->messageSendDateTime . '</div>
			</div>
			<hr />
			<div class="row">
				<div class="col-12">' . $content . '</div>
			</div>
		';

		$breadcrumbs = $this->messageBreadcrumbs('read', $messageID);
		$messageCard = new CardView('message_read',array('container','mb-1'),$breadcrumbs,array('col-12'),$header,$messageContent);

		$replies = $this->messageReplies($messageID);
		$replyForm = $this->messageReply($messageID);

		return $messageCard->card() . $replies . $replyForm;

	}

	private function messageReplies($messageID) {

		$message = new Message($messageID);
		$replies = $message->getReplies();

		$messageReplies = '<div id="message_replies">';
			$messageReplies .= '<div class="container mb-1">';
				for ($i = 0; $i < count($replies); $i++) {
					$reply = new Message($replies[$i]);
					$author = new User($reply->creator);
					$content = htmlspecialchars($reply->messageContent, ENT_QUOTES, 'UTF-8');
					$messageReplies .= '
						<div class="card ' . ($i%2==0?'bg-light ':'') . 'mb-1">
							<div class="card-body">
								<div class="row font-weight-lighter">
									<div class="col-12 col-md-8">'. $author->getUserDisplayName() . '</div>
									<div class="col-12 col-md-4 text-md-right">'. $reply->messageSendDateTime . '</div>
								</div>
								<hr />
								<div class="row">
									<div class="col-12">' . $content . '</div>
								</div>
							</div>
						</div>
					';
				}
			$messageReplies .= '</div>';
		$messageReplies .= '</div>';

		return $messageReplies;

	}

	private function messageReply($messageID) {

		$form = '
			<div id="message_reply" class="container">
				<div class="card">
					<div class="card-body">
						<form method="post" action="/' . Lang::prefix() . 'message/read/' . $messageID . '/">
							<input type="hidden" name="messageID" value="' . $messageID . '">
							<div class="form-row">
								<div class="form-group col-12">
									<textarea class="form-control" name="messageContent" placeholder="" rows="10"></textarea>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-12 col-sm-6 offset-sm-6 col-md-4 offset-md-8 col-lg-3 offset-lg-9 col-xl-2 offset-xl-10">
									<button type="submit" name="message-reply-submit" class="btn btn-primary btn-block">' . Lang::getLang('messageSubmitReply') . '</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		';

		return $form;

	}

	public function messageConfirmDelete($messageID) {

		$confirm = 'MESSAGE CONFIRM DELETE ' . $messageID;

		$header = Lang::getLang('messageConfirmDelete');
		$breadcrumbs = $this->messageBreadcrumbs('confirm-delete', $messageID);
		$card = new CardView('message_confirm_delete',array('container'),$breadcrumbs,array('col-12'),$header,$confirm);
		return $card->card();

	}

	private function messageBreadcrumbs($page, $messageID = null) {

		if (!in_array($page,array('inbox','draft','read','confirm-delete'))) { die('messageBreadcrumbs() error: bad $page'); }
		if (in_array($page,array('read','confirm-delete')) && is_null($messageID)) { die('messageBreadcrumbs() error: missing $messageID'); }

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
						if ($page=='confirm-delete') { $panko .= '<a href="/' . Lang::prefix() . 'message/read/' . $messageID . '/">' . Lang::getLang('message') . '</a>'; }
					$panko .= '</li>';
				}

				if ($page == 'confirm-delete') {
					$panko .= '<li class="breadcrumb-item active">' . Lang::getLang('messageConfirmDelete') . '</li>';
				}

			$panko .= '</ol>';
		$panko .= '</nav>';

		return $panko;

	}

}

?>