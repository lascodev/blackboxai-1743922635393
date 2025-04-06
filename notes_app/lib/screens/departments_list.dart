import 'package:flutter/material.dart';
import 'package:notes_app/models/department.dart';
import 'package:notes_app/services/api_service.dart';
import 'package:notes_app/screens/edit_department.dart';

class DepartmentsListScreen extends StatefulWidget {
  const DepartmentsListScreen({super.key});

  @override
  _DepartmentsListScreenState createState() => _DepartmentsListScreenState();
}

class _DepartmentsListScreenState extends State<DepartmentsListScreen> {
  final ApiService _apiService = ApiService();
  List<Department> _departments = [];
  bool _isLoading = true;
  String _error = '';

  @override
  void initState() {
    super.initState();
    _loadDepartments();
  }

  Future<void> _loadDepartments() async {
    try {
      final departments = await _apiService.getDepartments('your_token_here');
      setState(() {
        _departments = departments;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  Future<void> _deleteDepartment(int id) async {
    try {
      await _apiService.deleteDepartment(id, 'your_token_here');
      _loadDepartments(); // Rafraîchir la liste
    } catch (e) {
      setState(() => _error = e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Départements'),
        actions: [
          IconButton(
            icon: const Icon(Icons.add),
            onPressed: () => Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => const EditDepartmentScreen(),
              ),
            ),
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _error.isNotEmpty
              ? Center(child: Text(_error))
              : ListView.builder(
                  itemCount: _departments.length,
                  itemBuilder: (context, index) {
                    final department = _departments[index];
                    return ListTile(
                      title: Text(department.name),
                      trailing: IconButton(
                        icon: const Icon(Icons.delete),
                        onPressed: () => _deleteDepartment(department.id),
                      ),
                      onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => EditDepartmentScreen(department: department),
                        ),
                      ),
                    );
                  },
                ),
    );
  }
}