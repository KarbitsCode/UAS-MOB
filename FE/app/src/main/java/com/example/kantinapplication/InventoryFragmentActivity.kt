package com.example.kantinapplication

import android.app.AlertDialog
import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.TextView
import com.google.android.material.floatingactionbutton.FloatingActionButton

class InventoryFragmentActivity : Fragment() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
    }

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.fragment_inventory, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        // Start here
        view.findViewById<FloatingActionButton>(R.id.addInventoryFloatingActionButton).setOnClickListener {
            val dialog = LayoutInflater.from(context).inflate(R.layout.dialog_add_edit_inventory, null)
            val builder = AlertDialog.Builder(context).setView(dialog).create()
            dialog.findViewById<TextView>(R.id.titleTextView6).text = "TAMBAH PRODUK BARU"
            dialog.findViewById<Button>(R.id.inventorySaveActionButton).setOnClickListener {
                // TODO: insert ke database dan tampilkan ke inventoryRecyclerView
                // inventoryNameEditText, inventoryPriceEditText, inventoryStockEditText

                builder.dismiss()
            }
            builder.show()
        }

        view.findViewById<Button>(R.id.editStockActionButton)?.setOnClickListener {
            val dialog = LayoutInflater.from(context).inflate(R.layout.dialog_add_edit_inventory, null)
            val builder = AlertDialog.Builder(context).setView(dialog).create()
            dialog.findViewById<TextView>(R.id.titleTextView6).text = "EDIT DATA PRODUK"
            dialog.findViewById<Button>(R.id.inventorySaveActionButton).setOnClickListener {
                // TODO: update ke database dan tampilkan ke inventoryRecyclerView
                // inventoryNameEditText, inventoryPriceEditText, inventoryStockEditText

                builder.dismiss()
            }
            builder.show()
        }
    }
}