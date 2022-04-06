<?php
include_once("fpdf.php");
class PDF_MC_Table extends FPDF
{
     private $widthColum;
     private $widthHeader;
     private $autowidthsColums;
     public function getWidthColumn($column)
     {
          $max_width_column=0;
          for($j=0;$j<count($column);$j++)
          {
              if($max_width_column<$this->GetStringWidth($column[$j]))
              {
                  $max_width_column=$this->GetStringWidth($column[$j]);
              }
          }
          $this->widthColum=$max_width_column;
          return  $this->widthColum;
     }
     public function getWidthHeader($header)
     {
         for($j=0;$j<count($header);$j++)
         {
             $header_widths[$j]=$this->GetStringWidth($header[0]);
         }
         $this->widthsHeader=$header_widths;
         return $this->widthHeader;
     }
     public function getAutoWidthsColumns($width_pag,$header_width,$column_width)
     {
         for($i=0;$i<count($header_width);$i++)
            {
            if($header_width[$i]>$column_width[$i])
            {  
            $column_width_max[$i]=$header_width[$i];
            }else{
            $column_width_max[$i]=$column_width[$i];
            }
            }
            $columns_width_sum=0;
            for($i=0;$i<count($header_width);$i++)
            {
              $columns_width_sum+=$column_width_max[$i];  
            }
            $d=($width_pag-$columns_width_sum)/count($header_width);
            for($i=0;$i<count($header_width);$i++)
            {
            $column_width_max[$i]=$column_width_max[$i]+$d;
            }
            $this->autowidthsColums=$column_width_max;
            return $this->autowidthsColums;
     }
}
?>
