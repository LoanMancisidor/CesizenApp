import 'package:flutter/material.dart';
import '../repositories/auth_repository.dart';
import '../main.dart';
import 'change_password_screen.dart';

class ProfileScreen extends StatelessWidget {
  final String token;
  final AuthRepository _authRepository = AuthRepository();

  ProfileScreen({super.key, required this.token});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Mon Compte")),
      body: Padding(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          children: [
            const CircleAvatar(
              radius: 50, 
              backgroundColor: Colors.teal, 
              child: Icon(Icons.person, size: 50, color: Colors.white)
            ),
            const SizedBox(height: 30),
            Card(
            child: ListTile(
                leading: const Icon(Icons.lock_reset, color: Colors.teal),
                title: const Text("Réinitialiser le mot de passe"),
                trailing: const Icon(Icons.arrow_forward_ios),
                onTap: () {
                Navigator.push(
                    context,
                    MaterialPageRoute(
                    builder: (context) => ChangePasswordScreen(token: token),
                    ),
                );
                },
            ),
            ),
            
            const Spacer(),
            
            // Bouton de déconnexion
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () async {
                  await _authRepository.logout(token);
                  // On redirige vers la navigation "visiteur" (token null)
                  Navigator.pushAndRemoveUntil(
                    context,
                    MaterialPageRoute(builder: (context) => const MainNavigation(token: null)),
                    (route) => false,
                  );
                },
                icon: const Icon(Icons.logout),
                label: const Text("Se déconnecter"),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.red[50],
                  foregroundColor: Colors.red,
                  elevation: 0,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}