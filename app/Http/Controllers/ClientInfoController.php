<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientInfo;

class ClientInfoController extends Controller
{
    //
    public function getAllClientInfo(Request $request)
    {
        $cif = ClientInfo::paginate(10);

        $output = '<table class="table table-hover mb-2">
                        <tr>
                            <th>No.</th>
                            <th>Client id</th>
                            <th>Invoice No</th>
                            <th>Status</th>
                            <th>Date Paid</th>
                        </tr>';

        $count = ($cif->currentPage() - 1) * $cif->perPage();
        $button = '<i class="bi bi-eye-fill"></i>';
        foreach ($cif as $item) {
            $count++;
            $output .= "<tr>";
            $output .= "<td class='text-muted fw-bold'>" . $count . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . $item->id . "</td>";
            $output .= '<td><button id="view" class="view btn btn-primary" value="' . $item->invoice_no . '">' . $button . ' ' . $item->invoice_no . '</button></td>';
            $output .= "<td class='text-muted fw-bold'>" . $item->status . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . $item->updated_at . "</td>";
            $output .= "</tr>";
        }

        $output .= '</table>';
        $output .= $cif->links();

        return view('clients.table', ['cif_table' => $output]);
    }

    public function getSuccess(Request $request)
    {
        list($output, $status) = $this->getClientInfo($request->id);
        return view('pensopay.success', ['cif_table' => $output, 'status' => $status]);
    }

    public function getCancel(Request $request)
    {
        $output = $this->getClientInfo($request->id);
        return view('pensopay.cancel');
    }

    public function getClientInfo($id)
    {
        $cif = ClientInfo::where('id', $id)->get();
        $count = 0;
        $output = '<table class="table table-hover mb-2">
                        <tr>
                            <th>No.</th>
                            <th>Client id</th>
                            <th>Invoice No</th>
                            <th>Status</th>
                            <th>Date Paid</th>
                        </tr>';

        $button = '<i class="bi bi-eye-fill"></i>';
        $status = '';
        foreach ($cif as $item) {
            $status = $item->status;
            $count++;
            $output .= "<tr>";
            $output .= "<td class='text-muted fw-bold'>" . $count . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . $item->id . "</td>";
            $output .= '<td><button id="view" class="view btn btn-primary" value="' . $item->invoice_no . '">' . $button . ' ' . $item->invoice_no . '</button></td>';
            $output .= "<td class='text-muted fw-bold'>" . ucfirst($item->status) . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . $item->updated_at . "</td>";
            $output .= "</tr>";
        }

        $output .= '</table>';
        return [$output, ucfirst($status)];

    }
}