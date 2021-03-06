<?php 

class GjmaaMyFieldsImportForm {
	
	private $modelData;
	
	public function __construct($modelData = null){
		$this->modelData = $modelData;
	}
	
	public function getImportSelect($value = null,$id = 'settings_of_auctions',$name = 'settings_of_auctions',$class=''){
		/** @var $auctionsModel GjmaaMyAuctionsAllegro */
        $auctionsModel = $this->modelData['my_auctions_allegro'];
        $auctions = $auctionsModel->getAll();

        $settings = $this->modelData['gj_settings']->getSettings();
		$html = '<select class="'.$class.'" id="'.$id.'" name="'.$name.'" class="required" required>';
		$html .= '<option value="">' . __('Choose','gj_myauctions_allegro') . '</option>';
		foreach($auctions as $auction):
            $query = isset($auction['item_'.$auction['type_of_auctions'].'_query']) ? __('Query','gj_myauctions_allegro') . ': ' .  $auction['item_'.$auction['type_of_auctions'].'_query'] . ' ' : '';
		$optionName = $auction['id'] . '. ' . $query . ($auction['type_of_auctions'] == 'my_auctions' ? $settings['allegro_username'] : $auction[('item_'.$auction['type_of_auctions'].'_user')]) . ' (' . $auctionsModel->getNameOfSite($auction['site_allegro']) . ')';
			$html .= '<option value="'.$auction['id'].'"'.($value == $auction['id'] ? ' selected="selected"' : '').'>'.$optionName.'</option>';
		endforeach;
		$html .= '</select>';
		return $html;
	}

    public function generateProcessingFields(){
        $html = '<table class="import">'
        .'<tr><th>'.__('Import details?','gj_myauctions_allegro').'</th><td><input type="checkbox" name="import_details" id="import_details" /></td></tr>'
        .'<tr><th>'.__('Step','gj_myauctions_allegro') . ':</th><td id="step_import">0 / 1</td></tr>'
        .'<tr><th>'.__('Processing','gj_myauctions_allegro') . ':</th><td id="processing_import">0%</td></tr>'
        .'<tr><th>'.__('Imported auctions','gj_myauctions_allegro') . ':</th><td id="imported_auctions">0</td></tr>'
        .'<tr><th>'.__('All auctions','gj_myauctions_allegro') . ':</th><td id="all_auctions">0</td></tr>'
        .'</table>';
        return $html;
    }
	
	public function getImportButton(){
		return '<input class="button button-primary button-large" type="submit" value="'.__('Submit','gj_myauctions_allegro').'" />';
	}
	
	public function createImportForm(){
		return '<form id="importAuctions" action="'.admin_url('admin-ajax.php?page=gjmaa_auctions').'" method="POST">';
	}
	
	public function endImportForm(){
		return '<input type="hidden" name="action" value="gjmaa_do_import_auctions" />
		<input type="hidden" name="nonce" value="'.wp_create_nonce( 'gjmaa_do_import_auctions' ) . '" />
		</form>';
	}
}