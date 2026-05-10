import 'package:flutter/material.dart';
import 'screens/home_screen.dart';

void main() {
  runApp(const CesizenApp());
}

class CesizenApp extends StatelessWidget {
  const CesizenApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'CESIZen',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.teal),
        useMaterial3: true, // Pour un look moderne
      ),
      home: HomeScreen(),
    );
  }
}