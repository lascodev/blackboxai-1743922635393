import 'package:flutter/material.dart';
import 'package:notes_app/models/user.dart';
import 'package:notes_app/screens/login_screen.dart';
import 'package:notes_app/screens/home_screen.dart';
import 'package:notes_app/screens/profile_screen.dart';
import 'package:notes_app/services/auth_service.dart';

void main() {
  runApp(const NotesApp());
}

class NotesApp extends StatelessWidget {
  const NotesApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Gestion Notes',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        primarySwatch: Colors.blue,
        visualDensity: VisualDensity.adaptivePlatformDensity,
        appBarTheme: const AppBarTheme(
          centerTitle: true,
          elevation: 0,
        ),
      ),
      builder: (context, child) {
        ErrorWidget.builder = (FlutterErrorDetails errorDetails) {
          return Scaffold(
            appBar: AppBar(title: const Text('Erreur')),
            body: Center(
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Text(
                  'Une erreur est survenue:\n${errorDetails.exception}',
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: Colors.red),
                ),
              ),
            ),
          );
        };
        return child!;
      },
      initialRoute: '/',
      routes: {
        '/': (context) => FutureBuilder<User?>(
          future: AuthService().getUser(),
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Scaffold(
                body: Center(
                  child: CircularProgressIndicator(
                    strokeWidth: 2.0,
                    valueColor: AlwaysStoppedAnimation<Color>(Colors.blue),
                  ),
                ),
              );
            }
            
            if (snapshot.hasError) {
              return Scaffold(
                body: Center(
                  child: Text(
                    'Erreur de chargement: ${snapshot.error}',
                    style: const TextStyle(color: Colors.red),
                  ),
                ),
              );
            }

            return snapshot.hasData 
              ? const HomeScreen() 
              : const LoginScreen();
          },
        ),
        '/login': (context) => const LoginScreen(),
        '/home': (context) => const HomeScreen(),
        '/profile': (context) => ProfileScreen(
          user: ModalRoute.of(context)!.settings.arguments as User,
        ),
      },
    );
  }
}