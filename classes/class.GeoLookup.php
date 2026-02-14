<?php

class xGeoLookup {
   
   private $Flagarray               = [];
   private $Flagarray_DXCC          = [];
   private $Flagfile                = null;
   
   public function SetFlagFile($Flagfile) {
      if (file_exists($Flagfile) && (is_readable($Flagfile))) {
         $this->Flagfile = $Flagfile;
         return true;
      }
      return false;
   }
    
   public function LoadFlags() {
      if ($this->Flagfile === null) {
         return false;
      }

      $json_content = file_get_contents($this->Flagfile);
      if ($json_content === false) {
         return false;
      }

      $data = json_decode($json_content, true);
      if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
         return false;
      }

      $this->Flagarray = [];
      $this->Flagarray_DXCC = [];

      foreach ($data as $country_data) {
         $country_index = count($this->Flagarray);
         $this->Flagarray[$country_index] = [
            'Name' => $country_data['country_name'],
            'ISO' => $country_data['country_code']
         ];

         foreach ($country_data['prefixes'] as $prefix_entry) {
            $this->addPrefixToDXCC($prefix_entry, $country_index);
         }

         if (isset($country_data['sub_entities']) && is_array($country_data['sub_entities'])) {
            foreach ($country_data['sub_entities'] as $sub_entity_data) {
               $sub_entity_index = count($this->Flagarray);
               // If a sub entity has its own flag, then we can display it instead.  
               // Currently only Scotland, Northern Ireland, Wales and England but the code is generic
               if (array_key_exists('country_code',$sub_entity_data)) {
                  $sub_entity_country_code = $sub_entity_data['country_code'];
               } else {
                  $sub_entity_country_code = $country_data['country_code'];
               }
               $this->Flagarray[$sub_entity_index] = [
                  'Name' => $sub_entity_data['name'],
                  'ISO' => $sub_entity_country_code,
                  'IsSubEntity' => true,
                  'ParentName' => $country_data['country_name']
               ];
               foreach ($sub_entity_data['prefixes'] as $prefix_entry) {
                  $this->addPrefixToDXCC($prefix_entry, $sub_entity_index);
               }
            }
         }
      }
      return true;
   }
   
   private function addPrefixToDXCC($prefix_entry, $index) {
        if (strpos($prefix_entry, '-') === false) {
            $this->Flagarray_DXCC[$prefix_entry] = $index;
        } else {
            list($start_prefix, $end_prefix) = explode('-', $prefix_entry);
            
            $start_len = strlen($start_prefix);
            $end_len = strlen($end_prefix);

            if ($start_len === 2 && $end_len === 2) { 
                $first_char_start = ord($start_prefix[0]);
                $first_char_end = ord($end_prefix[0]);
                $second_char_start = ord($start_prefix[1]);
                $second_char_end = ord($end_prefix[1]);

                for ($c1 = $first_char_start; $c1 <= $first_char_end; $c1++) {
                    $s2_start = ($c1 == $first_char_start) ? $second_char_start : ord('A');
                    $s2_end = ($c1 == $first_char_end) ? $second_char_end : ord('Z');
                    
                    for ($c2 = $s2_start; $c2 <= $s2_end; $c2++) {
                        $this->Flagarray_DXCC[chr($c1) . chr($c2)] = $index;
                    }
                }
            } else if ($start_len === 1 && $end_len === 1) {
                for ($i = ord($start_prefix); $i <= ord($end_prefix); $i++) {
                    $this->Flagarray_DXCC[chr($i)] = $index;
                }
            }
        }
   }
   
   public function GetFlag($callsign) {
      $Image     = "";
      $Name = "";
      
      for ($Letters = 6; $Letters >= 1; $Letters--) {
         $Prefix = strtoupper(substr(trim($callsign), 0, $Letters));
         
         if (isset($this->Flagarray_DXCC[$Prefix])) {
            $index = $this->Flagarray_DXCC[$Prefix];
            $matched_entry = $this->Flagarray[$index];

            if (isset($matched_entry['IsSubEntity']) && $matched_entry['IsSubEntity'] === true) {
                if ($matched_entry['ParentName'] === 'United States' || $matched_entry['ParentName'] === 'Canada') {
                    // For US and Canadian sub-entities, return parent country name and flag
                    $Name = $matched_entry['ParentName'];
                    $Image = $matched_entry['ISO'];
                } else {
                    // For other sub-entities (e.g., UK, France), return sub-entity name and parent country flag
                    $Name = $matched_entry['Name'];
                    $Image = $matched_entry['ISO'];
                }
            } else {
                // This is a main country entry
                $Name = $matched_entry['Name'];
                $Image = $matched_entry['ISO'];
            }
            
            return [strtolower($Image), $Name];
         }
      }
      
      return ["undefined", "Undefined"]; 
   }
} 

