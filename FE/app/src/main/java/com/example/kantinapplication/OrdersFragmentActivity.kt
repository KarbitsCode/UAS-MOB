package com.example.kantinapplication

import android.app.AlertDialog
import android.os.Bundle
import androidx.fragment.app.Fragment
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
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
            val builder = AlertDialog.Builder(context)
                .setView(dialog)
                .create()
            dialog.findViewById<View>(R.id.orderSaveActionButton).setOnClickListener {
                // TODO: insert ke database dan tampilkan ke ordersRecyclerView
                // orderIdEditText, orderDetailsEditText

                builder.dismiss()
            }
            builder.show()
        }
    }
}