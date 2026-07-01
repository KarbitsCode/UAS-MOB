package com.example.kantinapplication

import android.app.AlertDialog
import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ArrayAdapter
import android.widget.Button
import android.widget.Spinner
import com.google.android.material.floatingactionbutton.FloatingActionButton

class OrdersFragmentActivity : Fragment() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
    }

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.fragment_orders, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        // Starts here
        view.findViewById<FloatingActionButton>(R.id.addOrderFloatingActionButton).setOnClickListener {
            val dialog = LayoutInflater.from(context).inflate(R.layout.dialog_add_order, null)
            val builder = AlertDialog.Builder(context).setView(dialog).create()
            dialog.findViewById<Button>(R.id.orderSaveActionButton).setOnClickListener {
                // TODO: insert ke database dan tampilkan ke ordersRecyclerView
                // orderIdEditText, orderDetailsEditText

                builder.dismiss()
            }

            // TODO: populasikan data dari db ke spinner
            // Example
            val listMenuKantin = listOf(
                "Nasi Goreng Spesial - Rp15.000",
                "Es Teh Manis - Rp5.000",
                "Ayam Geprek - Rp18.000"
            )

            val adapter = ArrayAdapter(
                requireContext(),
                android.R.layout.simple_spinner_item,
                listMenuKantin
            )
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
            val productSpinner = dialog.findViewById<Spinner>(R.id.productSpinner)
            productSpinner.adapter = adapter

            builder.show()
        }
    }
}