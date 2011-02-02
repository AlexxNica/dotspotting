<?php

	#
	# $Id$
	#

	include("include/init.php");

	loadlib("import");
	loadlib("formats");

	#################################################################

	login_ensure_loggedin("{$GLOBALS['cfg']['abs_root_url']}upload");

	if (! $GLOBALS['cfg']['enable_feature_import']){

		$GLOBALS['error']['uploads_disabled'] = 1;
		$smarty->display("page_upload.txt");
		exit();
	}

	#################################################################

	$crumb_key = 'upload';
	$crumb_ok = crumb_check($crumb_key);

	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	#

	$label = filter_strict(post_str('label'));
	$private = (post_str('private')) ? 1 : 0;
	$dots_index_on = filter_strict(post_str('dots_index_on'));
		
	$GLOBALS['smarty']->assign("label", $label);
	$GLOBALS['smarty']->assign("private", $private);
	$GLOBALS['smarty']->assign("dots_index_on", $dots_index_on);

	if (($crumb_ok) && ($_FILES['upload'])){

		$ok = 1;

		if ($_FILES['upload']['error']){

			$GLOBALS['error']['upload_error'] = 1;
			$GLOBALS['error']['upload_error_msg'] = $_FILES['upload']['error'];
			$ok = 0;
		}

		if ($ok){

			# first, figure out if this is a file we know how
			# do to anything with.

			if ($ext = post_str("format")){

				$map = formats_valid_import_map('key by extension');

				if (isset($map[$ext])){
					$_FILES['upload']['type'] = $map[$ext];
					$_FILES['upload']['extension'] = $ext;
				}

				else {
					$ok = 0;
				}
			}

			else {

				$ok = import_is_valid_mimetype($_FILES['upload']);
			}

			if (! $ok){

				$GLOBALS['error']['invalid_mimetype'] = 1;
			}

			# okay. try to pre-process the data

			else {

				$fingerprint = md5_file($_FILES['upload']);
				$GLOBALS['smarty']->assign("fingerprint", $fingerprint);

				$sheets = sheets_lookup_by_fingerprint($fingerprint, $GLOBALS['cfg']['user']);
				$GLOBALS['smarty']->assign_by_ref("sheets", $sheets);

				$more = array(
					'dots_index_on' => $dots_index_on,
				);

				$pre_process = import_process_file($_FILES['upload'], $more);

				$GLOBALS['smarty']->assign_by_ref("pre_process", $pre_process);

				# store the file somewhere in a pending bin?
			}
		}
	}

	else if (($crumb_ok) && (post_str("data"))){

		# FIX ME... where should these come from?

		$fingerprint = post_str('fingerprint');
		$mime_type = post_str('mime_type');
		$simplified = post_str('simplified');

		$raw_data = post_str("data");
		$data = json_decode($raw_data, "as hash");

		if (! $data){

			$GLOBALS['error']['missing_data'] = 1;
			$ok = 0;
		}

		else {

			$more = array(
				'return_dots' => 0,
				'dots_index_on' => $dots_index_on,
				'label' => $label,
				'mark_all_private' => $private,
				'mime_type' => $mime_type,
				'fingerprint' => $fingerprint,
				'simplified' => $simplified,
			);

			$import = import_process_data($GLOBALS['cfg']['user'], $data, $more);
			$GLOBALS['smarty']->assign_by_ref("import", $import);
		}
	}

	else {
		# nuthin'
	}

	$smarty->display("page_upload2.txt");
	exit();
?>