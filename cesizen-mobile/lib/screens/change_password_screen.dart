import 'package:flutter/material.dart';
import '../repositories/auth_repository.dart';

class ChangePasswordScreen extends StatefulWidget {
  final String token;
  const ChangePasswordScreen({super.key, required this.token});

  @override
  State<ChangePasswordScreen> createState() => _ChangePasswordScreenState();
}

class _ChangePasswordScreenState extends State<ChangePasswordScreen> {
  final _oldController = TextEditingController();
  final _newController = TextEditingController();
  final _confirmController = TextEditingController();
  final _authRepo = AuthRepository();
  bool _isLoading = false;

  void _handleUpdate() async {
    setState(() => _isLoading = true);
    
    final result = await _authRepo.updatePassword(
      widget.token, 
      _oldController.text, 
      _newController.text, 
      _confirmController.text
    );

    setState(() => _isLoading = false);

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(result['message']),
        backgroundColor: result['success'] ? Colors.green : Colors.red,
      ),
    );

    if (result['success']) Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Sécurité du compte")),
      body: Padding(
        padding: const EdgeInsets.all(25.0),
        child: Column(
          children: [
            TextField(
              controller: _oldController,
              obscureText: true,
              decoration: const InputDecoration(labelText: "Ancien mot de passe", border: OutlineInputBorder()),
            ),
            const SizedBox(height: 20),
            TextField(
              controller: _newController,
              obscureText: true,
              decoration: const InputDecoration(labelText: "Nouveau mot de passe", border: OutlineInputBorder()),
            ),
            const SizedBox(height: 20),
            TextField(
              controller: _confirmController,
              obscureText: true,
              decoration: const InputDecoration(labelText: "Confirmer le nouveau mot de passe", border: OutlineInputBorder()),
            ),
            const SizedBox(height: 30),
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _handleUpdate,
                style: ElevatedButton.styleFrom(backgroundColor: Colors.teal),
                child: _isLoading 
                  ? const CircularProgressIndicator(color: Colors.white) 
                  : const Text("Mettre à jour", style: TextStyle(color: Colors.white)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}