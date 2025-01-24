<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
Use \Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// use Maatwebsite\Excel\Concerns\RegistersEventListeners;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RFQItem implements FromView, WithTitle, WithEvents, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;
    protected $rfqs;

    
    function __construct($rfqs)
    {
       $this->rfqs = $rfqs;
    }

    public function view(): View {
       
        return view('export.RFQItem', ['rfqs' => $this->rfqs]);
        
	}

    public function title(): string
    {
        return 'List of RFQ';
    }


    //for designs
    public function registerEvents(): array
    {
        
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $highestColumn = $event->sheet->getHighestColumn();
                $highestRow = $event->sheet->getHighestRow();
                $event->sheet->getDelegate()->mergeCells('A1:G1');

                $event->sheet->getDelegate()->getStyle('A1')->applyFromArray(
                    array(
                        'font' => array(
                            'name'      =>  'Arial',
                            'size'      =>  15,
                            'bold'      => true
                        )
                    )
                );

                $event->sheet->getDelegate()->getStyle('A2:G2')->applyFromArray(
                    array(
                        'font' => array(
                            'name'      =>  'Arial',
                            'size'      =>  12,
                            'bold'      => true
                        )
                    )
                );

                $event->sheet->getDelegate()->getStyle('A2:'. $highestColumn . $highestRow)->applyFromArray(
                    array(
                        // 'font' => array(
                        //     'name' => 'Calibri',
                        //     'size' => 10,
                        // ),
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    )
                );
                
            },

         
        ];
    }

 
}
