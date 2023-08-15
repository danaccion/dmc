<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientInfo;
use App\Models\Client;

class ClientInfoController extends Controller
{

    //
    public function getAllClientInfo(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'updated_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $cif = ClientInfo::where(function ($query) use ($search) {
            $query->where('id', 'LIKE', '%' . $search . '%')
                ->orWhereHas('client', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('pay_no', 'LIKE', '%' . $search . '%');
                })
                ->orWhere('invoice_no', 'LIKE', '%' . $search . '%')
                ->orWhere('status', 'LIKE', '%' . $search . '%');
        })
            ->orderBy('created_at', $sortOrder ?: 'desc')
            ->paginate(10);


        $output = '<table class="table table-hover mb-2">
                    <tr>
                        <th>
                            Id
                            <a href="?search=' . $search . '&sort_by=id&sort_order=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort_by=id&sort_order=desc"><i class="bi bi-arrow-down"></i></a>
                        </th>
                        <th>
                            Client Id
                            <a href="?search=' . $search . '&sort_by=client_id&sort_order=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort_by=client_id&sort_order=desc"><i class="bi bi-arrow-down"></i></a>
                        </th>
                        <th>
                            Order Id
                        </th>
                        <th>
                            Company Name
                        </th>
                        <th>
                            Pay No
                        </th>
                        <th>
                            Invoice No
                            <a href="?search=' . $search . '&sort_by=invoice_no&sort_order=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort_by=invoice_no&sort_order=desc"><i class="bi bi-arrow-down"></i></a>
                        </th>
                        <th>
                            Status
                            <a href="?search=' . $search . '&sort_by=status&sort_order=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort_by=status&sort_order=desc"><i class="bi bi-arrow-down"></i></a>
                        </th>
                        <th>
                            Currency
                            <a href="?search=' . $search . '&sort_by=currency&sort_order=asc"><i class="bi bi-arrow-up"></i></a>
                            <a href="?search=' . $search . '&sort_by=currency&sort _order=desc"><i class="bi bi-arrow-down"></i></a>
                    </th>
                    <th>
                        Amount
                    </th>
                    <th>
                        Date Paid
                        <a href="?search=' . $search . '&sort_by=updated_at&sort_order=asc"><i class="bi bi-arrow-up"></i></a>
                        <a href="?search=' . $search . '&sort_by=updated_at&sort_order=desc"><i class="bi bi-arrow-down"></i></a>
                    </th>
                    <th>
                        Date Created
                        <a href="?search=' . $search . '&sort_by=created_at&sort_order=asc"><i class="bi bi-arrow-up"></i></a>
                        <a href="?search=' . $search . '&sort_by=created_at&sort_order=desc"><i class="bi bi-arrow-down"></i></a>
                    </th>
                    <th>
                    Recall
                    <a href="?search=' . $search . '&sort_by=created_at&sort_order=asc"><i class="bi bi-arrow-up"></i></a>
                    <a href="?search=' . $search . '&sort_by=created_at&sort_order=desc"><i class="bi bi-arrow-down"></i></a>
                </th>
                    <th>
                    Delete
                    <a href="?search=' . $search . '&sort_by=created_at&sort_order=asc"><i class="bi bi-arrow-up"></i></a>
                    <a href="?search=' . $search . '&sort_by=created_at&sort_order=desc"><i class="bi bi-arrow-down"></i></a>
                </th>

                </tr>';

        $count = ($cif->currentPage() - 1) * $cif->perPage();
        $button = '<i class="bi bi-eye-fill"></i>';
        foreach ($cif as $item) {
            $count++;
            $output .= "<tr>";
            $output .= "<td class='text-muted fw-bold'>" . $count . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . $item->id . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . optional($item->client->orderId)->id . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . optional($item->client)->name . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . optional($item->client)->pay_no . "</td>";
            $output .= '<td><button id="view" class="view btn btn-primary" value="' . $item->invoice_no . '">' . $button . ' ' . $item->invoice_no . '</button></td>';
            $output .= "<td class='text-muted fw-bold'>" . ($item->status === 'Approved' ? 'Paid' : 'Unpaid') . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . $item->currency . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . number_format($item->orig_amount + (($item->additional_fee / 100) * $item->orig_amount), 2, ',', '.') . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . $item->created_at . "</td>";
            $output .= "<td class='text-muted fw-bold'>" . $item->updated_at . "</td>";
            $output .= '<td><button class="showdetails btn btn-success" data-id="' . $item->id . '"><span class="bi bi-binoculars" style="color:white; font-size:22px;"></span></button></td>';
            $output .= '<td><button class="delete btn btn-danger" data-id="' . $item->id . '"><span class="bi bi-trash" style="color:white; font-size:22px;"></span></button></td>';


            // $output .= '<td><button class="delete btn btn-danger" data-id="' . $item->id . '">Delete</button></td>';
            $output .= "</tr>";
        }

        $output .= '</table>';
        $output .= $cif->appends(['search' => $search, 'sort_by' => $sortBy, 'sort_order' => $sortOrder])->links();

        return view('clients.table', ['cif_table' => $output]);
    }

    public function deletehistory($id)
    {
        // Perform the delete operation based on the $id
        // You can use Laravel's Eloquent or Query Builder for this
        // For example, using Eloquent:

        $client_inf = ClientInfo::where('id', $id)->get();
        Client::where('id', $client_inf[0]->client_id)->delete();
        ClientInfo::where('id', $id)->delete();

        return response()->json(['message' => 'Data deleted successfully.']);
    }
    public function get_details($id)
    {
        // Perform the delete operation based on the $id
        // You can use Laravel's Eloquent or Query Builder for this
        // For example, using Eloquent:

        $client_inf = ClientInfo::where('id', $id)->get();
        $client_main = Client::where('id', $client_inf[0]->client_id)->get();
        return view('clients.getdetails', ['client_info' => $client_inf, 'client_main' => $client_main,]);
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
        $cif2 = ClientInfo::where('id', $id)->get();
        $output = '';
        $output .= "<h1 style='text-align: center;line-height: 100px;'>Receipt</h1>";
        foreach ($cif2 as $item) {
            $timestamp = strtotime($item->updated_at);
            $formattedDate = date("F j, Y", $timestamp);
            $output .= "<p class=''>Date: " . $formattedDate . " </p>";
            $output .= "<p class=''>Receipt No: " . $item->transaction_id . " </p>";
        }
        $count = 0;
        $output .= '<table class="table table-hover mb-2">
                        <tr>
                            <th>No.</th>
                            <th>Client id</th>
                            <th>Invoice No</th>
                            <th>Status</th>
                            <th>Price</th>
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
            $output .= '<td> ' . $item->invoice_no . '</td>';
            $output .= "<td class='text-muted fw-bold'>" . ($item->status === 'Approved' ? 'Paid' : 'Unpaid') . "</td>";
            $amount = number_format($item->orig_amount + (($item->additional_fee / 100) * $item->orig_amount), 2, ',', '.');
            $output .= "<td class='text-muted fw-bold'>" . $amount. "</td>";
            $output .= "<td class='text-muted fw-bold'>" . $item->updated_at . "</td>";
            $output .= "</tr>";
        }
        $output .= '</table>';
        $output .= "<p class='text-muted fw-bold' style='text-align: right;line-height: 100px;'>Total: $item->currency $item->orig_amount </p>";
        if ($status != 'approved' || ucfirst($item->status) != 'approved') {
            $output .= "<button class='btn btn-primary' id='print' name='print'>
            <i class='fas fa-print'></i> 
         </button>";
        }
        return [$output, ucfirst($status)];

    }
}