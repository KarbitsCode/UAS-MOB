package com.example.kantinapplication.utils

import android.app.Activity
import android.content.Context
import android.content.Intent

inline fun <reified T : Activity> Context.goingTo(): Intent {
    return Intent(this, T::class.java)
}
