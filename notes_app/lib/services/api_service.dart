import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:notes_app/models/note.dart';
import 'package:notes_app/models/department.dart';

class ApiService {
  static const String baseUrl = 'http://localhost:8000/api'; // À adapter
  static const Map<String, String> headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  };

  // Authentification
  Future<Map<String, dynamic>> login(String username, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth.php'),
      headers: headers,
      body: jsonEncode({
        'username': username,
        'password': password
      }),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Échec de la connexion');
    }
  }

  // Gestion des notes
  Future<List<Note>> getNotes(String token) async {
    final response = await http.get(
      Uri.parse('$baseUrl/notes.php'),
      headers: {...headers, 'Authorization': 'Bearer $token'},
    );

    if (response.statusCode == 200) {
      final List<dynamic> data = jsonDecode(response.body);
      return data.map((json) => Note.fromJson(json)).toList();
    } else {
      throw Exception('Échec du chargement des notes');
    }
  }

  Future<void> createNote(Note note, String token) async {
    final response = await http.post(
      Uri.parse('$baseUrl/notes.php'),
      headers: {...headers, 'Authorization': 'Bearer $token'},
      body: jsonEncode(note.toJson()),
    );

    if (response.statusCode != 201) {
      throw Exception('Échec de la création de la note');
    }
  }

  // Gestion des départements
  Future<List<Department>> getDepartments(String token) async {
    final response = await http.get(
      Uri.parse('$baseUrl/departments.php'),
      headers: {...headers, 'Authorization': 'Bearer $token'},
    );

    if (response.statusCode == 200) {
      final List<dynamic> data = jsonDecode(response.body);
      return data.map((json) => Department.fromJson(json)).toList();
    } else {
      throw Exception('Échec du chargement des départements');
    }
  }

  Future<void> createDepartment(Department department, String token) async {
    final response = await http.post(
      Uri.parse('$baseUrl/departments.php'),
      headers: {...headers, 'Authorization': 'Bearer $token'},
      body: jsonEncode(department.toJson()),
    );

    if (response.statusCode != 201) {
      throw Exception('Échec de la création du département');
    }
  }

  Future<void> updateDepartment(Department department, String token) async {
    final response = await http.put(
      Uri.parse('$baseUrl/departments.php?id=${department.id}'),
      headers: {...headers, 'Authorization': 'Bearer $token'},
      body: jsonEncode(department.toJson()),
    );

    if (response.statusCode != 200) {
      throw Exception('Échec de la mise à jour du département');
    }
  }

  Future<void> deleteDepartment(int id, String token) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/departments.php?id=$id'),
      headers: {...headers, 'Authorization': 'Bearer $token'},
    );

    if (response.statusCode != 200) {
      throw Exception('Échec de la suppression du département');
    }
  }
}