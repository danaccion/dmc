<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientInfo;

class ClientInfoController extends Controller
{
    //
    public function getAllClientInfo(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'desc'); // Default sort order is descending
    
        $cif = ClientInfo::where(function ($query) use ($search) {
            $query->where('id', 'LIKE', '%' . $search . '%')
                ->orWhereHas('client', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                })
                ->orWhere('invoice_no', 'LIKE', '%' . $search . '%')
                ->orWhere('status', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('updated_at', $sort) // Sort by 'updated_at' column with the specified sort order
        ->paginate(10);
    
        $output = '<table class="table table-hover mb-2">
                    <tr>
                        <th>
                            Id
                            <a href="?search=' . $search . '&sort=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort=desc"><i class="bi bi-arrow-down"></i></a>
                        </th>
                        <th>
                            Client Id
                            <a href="?search=' . $search . '&sort=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort=desc"><i class="bi bi-arrow-down"></i></a>
                        </th>
                        <th>
                            Company Name
                            <a href="?search=' . $search . '&sort=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort=desc"><i class="bi bi-arrow-down"></i></a>
                        </th>
                        <th>
                            Pay No
                            <a href="?search=' . $search . '&sort=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort=desc"><i class="bi bi-arrow-down"></i></a>
                        </th>
                        <th>
                            Invoice No
                            <a href="?search=' . $search . '&sort=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort=desc"><i class="bi bi-arrow-down"></i></a>
                        </th>
                        <th>
                            Status
                            <a href="?search=' . $search . '&sort=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort=desc"><i class="bi bi-arrow-down"></i></a>
                        </th>
                        <th>
                            Date Paid
                            <a href="?search=' . $search . '&sort=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort=desc"><i class="bi bi-arrow-down"></i></a>
                        </th>
                    </tr>';

    
    
        $count = ($cif->currentPage() - 1) * $cif->perPage();
        $button = '<i class="bi bi-eye-fill"></i>';
        foreach ($cif as $item) {
            $count++;
            $output .= "<tr>";
            $output .= "<td class='text-muted fw-bold'>" . $count . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . $item->id . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . optional($item->client)->name . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . optional($item->client)->pay_no . "</td>";
            $output .= '<td><button id="view" class="view btn btn-primary" value="' . $item->invoice_no . '">' . $button . ' ' . $item->invoice_no . '</button></td>';
            $output .= "<td class='text-muted fw-bold'>" . $item->status . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . $item->updated_at . "</td>";
            $output .= "</tr>";
        }
    
        $output .= '</table>';
        $output .= $cif->appends(['search' => $search])->links();
    
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