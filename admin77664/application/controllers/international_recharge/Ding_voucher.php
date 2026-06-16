<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ding_voucher extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		error_reporting(E_ALL ^ E_NOTICE);
		$this->load->model(array('admin_model', 'emailtemplate_model', 'sms_model', 'notification_model', 'geneal_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	}

	public function index()
	{
		$this->admin_model->authCheck('view_data');
		$data['error'] = '';
		$data['activeMenu'] = 'international_recharge';
		$data['activeSubMenu'] = 'ding_voucher';

		$whereCon = array();
		if ($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField = trim((string)$this->input->get('searchField'));
			$sValue = trim((string)$this->input->get('searchValue'));
			if (in_array($sField, array('sequence_order')) && is_numeric($sValue)):
				$whereCon['where'] = array($sField => (int)$sValue);
			else:
				$whereCon['like'] = array('0' => $sField, '1' => $sValue);
			endif;
			$data['searchField'] = $sField;
			$data['searchValue'] = $sValue;
		else:
			$whereCon['like'] = '';
			$data['searchField'] = '';
			$data['searchValue'] = '';
		endif;

		$shortField = array('sequence_order' => 1, 'name' => 1);
		$baseUrl = getCurrentControllerPath('index');
		$this->session->set_userdata('ALLDINGVOUCHERDATA', currentFullUrl());
		$qStringdata = explode('?', currentFullUrl());
		$suffix = isset($qStringdata[1]) && $qStringdata[1] ? '?' . $qStringdata[1] : '';
		$tblName = 'ding_voucher_list';
		$totalRows = (int)$this->common_model->getData('count', $tblName, $whereCon, $shortField, '0', '0');

		if ($this->input->get('showLength') == 'All'):
			$perPage = $totalRows > 0 ? $totalRows : 1;
			$data['perpage'] = $this->input->get('showLength');
		elseif ($this->input->get('showLength')):
			$perPage = (int)$this->input->get('showLength');
			$data['perpage'] = $this->input->get('showLength');
		else:
			$perPage = (int)SHOW_NO_OF_DATA;
			$data['perpage'] = SHOW_NO_OF_DATA;
		endif;

		$uriSegment = getUrlSegment();
		$data['PAGINATION'] = adminPagination($baseUrl, $suffix, $totalRows, $perPage, $uriSegment);
		$page = $this->uri->segment(getUrlSegment()) ? (int)$this->uri->segment(getUrlSegment()) : 0;
		$data['forAction'] = $baseUrl;

		if ($totalRows):
			$first = $page + 1;
			$data['first'] = $first;
			$pageData = ($data['perpage'] == 'All') ? $totalRows : (int)$data['perpage'];
			$last = (($page + $pageData) > $totalRows) ? $totalRows : ($page + $pageData);
			$data['noOfContent'] = 'Showing ' . $first . '-' . $last . ' of ' . $totalRows . ' items';
		else:
			$data['first'] = 1;
			$data['noOfContent'] = '';
		endif;

		$data['ALLDATA'] = $this->normalizeVoucherImages($this->common_model->getData('multiple', $tblName, $whereCon, $shortField, $perPage, $page));
		$this->layouts->set_title('Ding Voucher List | International Recharge | Instwin');
		$this->layouts->admin_view('international_recharge/ding/voucher/index', array(), $data);
	}

	public function addeditdata($editId = '')
	{
		$data['error'] = '';
		$data['activeMenu'] = 'international_recharge';
		$data['activeSubMenu'] = 'ding_voucher';
		$tblName = 'ding_voucher_list';

		if ($editId):
			$this->admin_model->authCheck('edit_data');
			try {
				$oid = new MongoDB\BSON\ObjectID($editId);
			} catch (Exception $e) {
				redirect(getCurrentControllerPath('index'));
				return;
			}
			$data['EDITDATA'] = $this->normalizeVoucherImage($this->common_model->getData('single', $tblName, array('where' => array('_id' => $oid))));
			if (empty($data['EDITDATA'])):
				$this->session->set_flashdata('alert_error', 'Voucher data not found.');
				redirect(getCurrentControllerPath('index'));
				return;
			endif;
		else:
			$this->admin_model->authCheck('add_data');
			$data['EDITDATA'] = array();
		endif;

		$data['PROVIDERS'] = $this->_get_provider_options();

		if ($this->input->post('SaveChanges')):
			$error = 'NO';
			$this->form_validation->set_rules('name', 'Voucher Name', 'trim|required');
			$this->form_validation->set_rules('message', 'Message', 'trim|required');
			$this->form_validation->set_rules('status', 'Status', 'trim|required');
			$this->form_validation->set_rules('sequence_order', 'Sequence Order', 'trim|required|numeric');
			$this->form_validation->set_rules('SaveChanges', 'SaveChanges', 'trim|required');
			$providerCodes = $this->input->post('provider_codes');
			if (!is_array($providerCodes)) {
				$providerCodes = array();
			}
			$providerCodes = array_values(array_filter(array_map('trim', $providerCodes)));
			if (empty($providerCodes)) {
				$error = 'YES';
				$data['error'] = 'Please select at least one provider.';
			}

			if ($this->form_validation->run() && $error == 'NO'):
				$providerNames = array();
				foreach ($providerCodes as $providerCode):
					$providerNames[] = $this->_provider_name_by_code($providerCode, $data['PROVIDERS']);
				endforeach;
				$providerNames = array_values(array_unique(array_filter($providerNames)));
				$param['name'] = addslashes(trim((string)$this->input->post('name')));
				$param['message'] = addslashes(trim((string)$this->input->post('message')));
				$param['provider_codes'] = $providerCodes;
				$param['provider_names'] = $providerNames;
				$param['provider_name'] = implode(', ', $providerNames);
				$param['sequence_order'] = (int)$this->input->post('sequence_order');
				$param['status'] = trim((string)$this->input->post('status')) == 'I' ? 'I' : 'A';

				if (!empty($_FILES['image']['name'])):
					$uploadErr = isset($_FILES['image']['error']) ? (int) $_FILES['image']['error'] : UPLOAD_ERR_NO_FILE;
					if ($uploadErr !== UPLOAD_ERR_OK):
						$uploadErrMsg = array(
							UPLOAD_ERR_INI_SIZE   => 'Image exceeds server upload_max_filesize.',
							UPLOAD_ERR_FORM_SIZE  => 'Image exceeds form size limit.',
							UPLOAD_ERR_PARTIAL    => 'Image was only partially uploaded.',
							UPLOAD_ERR_NO_FILE    => 'No image was received.',
							UPLOAD_ERR_NO_TMP_DIR => 'Server has no temp folder for uploads (check upload_tmp_dir).',
							UPLOAD_ERR_CANT_WRITE => 'Could not write temp upload to disk.',
							UPLOAD_ERR_EXTENSION  => 'Upload blocked by a PHP extension.',
						);
						$data['error'] = isset($uploadErrMsg[$uploadErr]) ? $uploadErrMsg[$uploadErr] : 'Image upload error code ' . $uploadErr . '.';
						$error = 'YES';
					else:
						$originalFileName = str_replace(' ', '_', $_FILES['image']['name']);
						$tmpName = $_FILES['image']['tmp_name'];
						$fileInfo = pathinfo($originalFileName);
						$extension = strtolower($fileInfo['extension'] ?? '');
						$allowedExt = array('jpg', 'jpeg', 'png', 'gif', 'webp');
						if (!in_array($extension, $allowedExt, true)):
							$data['error'] = 'Invalid image format. Allowed: jpg, jpeg, png, gif, webp.';
							$error = 'YES';
						else:
							$targetDir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR;
							if (!is_dir($targetDir)):
								if (!@mkdir($targetDir, 0775, true) && !is_dir($targetDir)):
									$data['error'] = 'Could not create folder: ' . $targetDir;
									$error = 'YES';
								endif;
							endif;
							if ($error === 'NO' && is_dir($targetDir) && !is_writable($targetDir)):
								$data['error'] = 'Folder is not writable by PHP: ' . $targetDir . ' (set owner to www-data/apache or chmod; on RHEL/CentOS check SELinux: chcon -R -t httpd_sys_rw_content_t).';
								$error = 'YES';
							endif;
							if ($error === 'NO'):
								$newFileName = $this->common_model->microseconds() . '.' . $extension;
								$targetPath = $targetDir . $newFileName;
								if (!is_uploaded_file($tmpName)):
									$data['error'] = 'Invalid upload (tmp file missing). Check PHP open_basedir includes system temp.';
									$error = 'YES';
								elseif (!move_uploaded_file($tmpName, $targetPath)):
									$last = error_get_last();
									$hint = ($last && !empty($last['message'])) ? ' PHP: ' . $last['message'] : '';
									$data['error'] = 'Could not save image to ' . $targetPath . '.' . $hint;
									$error = 'YES';
								else:
									@chmod($targetPath, 0644);
									$param['image'] = 'assets/admin/image/' . $newFileName;
								endif;
							endif;
						endif;
					endif;
				endif;

				if ($error == 'NO'):
					if ($editId == ''):
						$param['creation_ip'] = currentIp();
						$param['creation_date'] = (int)$this->timezone->utc_time();
						$param['created_by'] = (int)$this->session->userdata('ADMIN_ID');
						if (!isset($param['image'])):
							$param['image'] = '';
						endif;
						$this->common_model->addData($tblName, $param);
						$this->session->set_flashdata('alert_success', lang('addsuccess'));
					else:
						$param['update_ip'] = currentIp();
						$param['update_date'] = (int)$this->timezone->utc_time();
						$param['updated_by'] = (int)$this->session->userdata('ADMIN_ID');
						$this->common_model->editData($tblName, $param, '_id', $oid);
						$this->session->set_flashdata('alert_success', lang('updatesuccess'));
					endif;
					redirect(correctLink('ALLDINGVOUCHERDATA', getCurrentControllerPath('index')));
				endif;
			endif;
		endif;

		$this->layouts->set_title(($editId ? 'Edit' : 'Add') . ' Ding Voucher | International Recharge | Instwin');
		$this->layouts->admin_view('international_recharge/ding/voucher/addeditdata', array(), $data);
	}

	public function changestatus($changeStatusId = '', $statusType = '')
	{
		$this->admin_model->authCheck('edit_data');
		try {
			$oid = new MongoDB\BSON\ObjectID($changeStatusId);
		} catch (Exception $e) {
			redirect(correctLink('ALLDINGVOUCHERDATA', getCurrentControllerPath('index')));
			return;
		}
		$param['status'] = ($statusType == 'I') ? 'I' : 'A';
		$this->common_model->editData('ding_voucher_list', $param, '_id', $oid);
		$this->session->set_flashdata('alert_success', lang('statussuccess'));
		redirect(correctLink('ALLDINGVOUCHERDATA', getCurrentControllerPath('index')));
	}

	public function deletedata($deleteId = '')
	{
		$this->admin_model->authCheck('delete_data');
		try {
			$oid = new MongoDB\BSON\ObjectID($deleteId);
		} catch (Exception $e) {
			redirect(correctLink('ALLDINGVOUCHERDATA', getCurrentControllerPath('index')));
			return;
		}
		$this->common_model->deleteData('ding_voucher_list', '_id', $oid);
		$this->session->set_flashdata('alert_success', lang('deletesuccess'));
		redirect(correctLink('ALLDINGVOUCHERDATA', getCurrentControllerPath('index')));
	}

	private function _get_provider_options()
	{
		$out = array();
		$providers = $this->common_model->getData('multiple', 'ding_provider_list', array('where' => array('status' => 'A')), array('Name' => 1));
		if (!empty($providers) && is_array($providers)):
			foreach ($providers as $p):
				$p = is_object($p) ? json_decode(json_encode($p), true) : $p;
				if (!is_array($p)) {
					continue;
				}
				if (isset($p['ProviderCode']) || isset($p['Name'])):
					$code = isset($p['ProviderCode']) ? trim((string)$p['ProviderCode']) : trim((string)$p['Name']);
					$name = isset($p['Name']) ? trim((string)$p['Name']) : $code;
					if ($code !== ''):
						$out[$code] = $name;
					endif;
				endif;
				if (!empty($p['list']) && is_array($p['list'])):
					foreach ($p['list'] as $item):
						$item = is_object($item) ? json_decode(json_encode($item), true) : $item;
						if (!is_array($item)) {
							continue;
						}
						$code = isset($item['ProviderCode']) ? trim((string)$item['ProviderCode']) : trim((string)($item['Name'] ?? ''));
						$name = isset($item['Name']) ? trim((string)$item['Name']) : $code;
						if ($code !== ''):
							$out[$code] = $name;
						endif;
					endforeach;
				endif;
			endforeach;
		endif;
		asort($out);
		return $out;
	}

	private function _provider_name_by_code($code, $providers)
	{
		$code = trim((string)$code);
		if ($code === '') {
			return '';
		}
		if (!empty($providers[$code])) {
			return $providers[$code];
		}
		return $code;
	}

	private function normalizeVoucherImages($rows)
	{
		if (empty($rows) || !is_array($rows)) {
			return $rows;
		}
		foreach ($rows as $index => $row) {
			$rows[$index] = $this->normalizeVoucherImage($row);
		}
		return $rows;
	}

	private function normalizeVoucherImage($row)
	{
		if (empty($row)) {
			return $row;
		}
		$row = is_object($row) ? json_decode(json_encode($row), true) : $row;
		if (!is_array($row)) {
			return $row;
		}

		$image = trim((string)($row['image'] ?? ($row['voucher_image'] ?? ($row['image_url'] ?? ''))));
		if ($image === '') {
			return $row;
		}
		if (preg_match('/^https?:\/\//i', $image)) {
			$row['image'] = $image;
			return $row;
		}

		$image = str_replace('\\', '/', $image);
		$rootBase = rtrim(base_url(), '/') . '/';
		$publicBase = str_replace('/admin77664/', '/', $rootBase);

		if (strpos($image, 'admin77664/assets/') !== false) {
			$relativeImage = ltrim(substr($image, strpos($image, 'admin77664/assets/')), '/');
			$row['image'] = $publicBase . $relativeImage;
		} elseif (strpos($image, 'assets/') === 0) {
			$row['image'] = $rootBase . ltrim($image, '/');
		} else {
			$row['image'] = $rootBase . 'assets/admin/image/' . ltrim($image, '/');
		}

		return $row;
	}
}
