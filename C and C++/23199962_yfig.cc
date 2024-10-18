#include <GL/glut.h>
#include <stdlib.h>
#include <math.h>
#include <vector>

#define DEG2RAD 3.14159/180.0

int width = 800;  
int height = 600; 
enum DrawMode { NONE, LINE, RECTANGLE,CIRCLE };
DrawMode currentDrawMode = NONE;
bool drawingLine = false;      
bool firstClick = true;        
GLfloat lineStartX = 0.0, lineStartY = 0.0; 
GLfloat lineEndX = 0.0, lineEndY = 0.0;    
bool isDrawingPreview = false; 
float tempEndX = 0.0, tempEndY = 0.0; 
float zoomFactor = 1.0; 
#if !defined(GLUT_WHEEL_UP)  
# define GLUT_WHEEL_UP 3  
# define GLUT_WHEEL_DOWN 4  
#endif 
GLfloat currentPenThickness = 1; 
GLfloat currentColor[3] = {0.0f, 0.0f, 0.0f};
struct Line {
    float startX, startY;
    float endX, endY;
    GLfloat thickness;
    GLfloat color[3]; 
};
std::vector<Line> lines; 
struct Rectangle {
    float bottomLeftX, bottomLeftY;
    float topRightX, topRightY;
    GLfloat thickness;
    GLfloat color[3]; 
};

std::vector<Rectangle> rectangles; 
struct Circle {
    float centerX, centerY;
    float radius;
    GLfloat thickness;
    GLfloat color[3]; 
};

std::vector<Circle> circles; 

float offsetX = 0.0f, offsetY = 0.0f;


int lastMouseX = 0, lastMouseY = 0;


bool middleButtonPressed = false;

void init() {
    glClearColor(1.0, 1.0, 1.0, 1.0); 
}

void display() {
    glClear(GL_COLOR_BUFFER_BIT); 

    glMatrixMode(GL_PROJECTION);
    glLoadIdentity();
    gluOrtho2D((-width / 2 - offsetX)*zoomFactor, (width / 2 - offsetX)*zoomFactor, (-height / 2 - offsetY)*zoomFactor, (height / 2 - offsetY)*zoomFactor);

    
    glColor3f(0.0, 0.0, 1.0); 
    glLineWidth(1);
    glBegin(GL_LINES);
        
        glVertex2f((-width / 2- offsetX)*zoomFactor, 0.0);
        glVertex2f((width / 2- offsetX)*zoomFactor, 0.0);
        
        glVertex2f(0.0, (-height / 2- offsetY)*zoomFactor);
        glVertex2f(0.0, (height / 2- offsetY)*zoomFactor);
            
        
    glEnd();
    glColor3f(currentColor[0],currentColor[1],currentColor[2]); 
   
    glLineWidth(currentPenThickness);
    
    for (const auto& line : lines) {
    glLineWidth(line.thickness); 
        glColor3fv(line.color); 
        glBegin(GL_LINES);
        glVertex2f(line.startX, line.startY);
        glVertex2f(line.endX, line.endY);
        glEnd();
    }
    for (const auto& rect : rectangles) {
        glLineWidth(rect.thickness);
        glColor3fv(rect.color);
        glBegin(GL_LINE_LOOP); 
            glVertex2f(rect.bottomLeftX, rect.bottomLeftY);
            glVertex2f(rect.bottomLeftX, rect.topRightY);
            glVertex2f(rect.topRightX, rect.topRightY);
            glVertex2f(rect.topRightX, rect.bottomLeftY);
        glEnd();
    }
       
    for (const auto& circle : circles) {
        glLineWidth(circle.thickness);
        glColor3fv(circle.color);

        glBegin(GL_LINE_LOOP);
        for(int i = 0; i <= 360; i++) { 
            float degInRad = i * DEG2RAD;
            glVertex2f(cos(degInRad) * circle.radius + circle.centerX, 
                       sin(degInRad) * circle.radius + circle.centerY);
        }
        glEnd();
    }
        
    glutSwapBuffers();
}

void reshape(int w, int h) {
    
    width = w;
    height = h;

    glViewport(0, 0, w, h);
    glMatrixMode(GL_PROJECTION);
    glLoadIdentity();
   
    gluOrtho2D((-w / 2 - offsetX)*zoomFactor, (w / 2 - offsetX)*zoomFactor, (-h / 2 - offsetY)*zoomFactor, (h / 2 - offsetY)*zoomFactor);
    glMatrixMode(GL_MODELVIEW);
    
}
void keyboard(unsigned char key, int x, int y) {
    switch (key) {
        case 'l':
        case 'L':
            currentDrawMode = LINE; 
            firstClick = true;
            break;
        case 'r':
        case 'R':
            currentDrawMode = RECTANGLE; 
            firstClick = true;
            break;
        case 'c':
        case 'C':
            currentDrawMode = CIRCLE; 
            firstClick = true;
            break;
    }
}

void mouse(int button, int state, int x, int y) {
int mod = glutGetModifiers();
    if (button == GLUT_MIDDLE_BUTTON && state == GLUT_DOWN) {
        
        middleButtonPressed = true;
            lastMouseX = x;
            lastMouseY = y;
    } else if(button == GLUT_MIDDLE_BUTTON && state == GLUT_UP){
    middleButtonPressed = false;
    
    }else if (button == GLUT_LEFT_BUTTON && state == GLUT_DOWN ) {
    GLfloat transformedX = (x - width / 2- offsetX)*zoomFactor;
        GLfloat transformedY = ((height - y) - height / 2- offsetY)*zoomFactor;
if (firstClick) {
            
            lineStartX = transformedX;
            lineStartY = transformedY; 
            firstClick = false;
        } else {

            
            if (currentDrawMode == LINE) {
                Line newLine = {lineStartX, lineStartY, transformedX, transformedY,currentPenThickness,{currentColor[0], currentColor[1], currentColor[2]}};
                lines.push_back(newLine);
            } else if (currentDrawMode == RECTANGLE) {
                Rectangle newRect = {lineStartX, lineStartY, transformedX, transformedY, currentPenThickness, {currentColor[0], currentColor[1], currentColor[2]}};
                rectangles.push_back(newRect);
            }else if (currentDrawMode == CIRCLE) {
                float dx = transformedX - lineStartX;
                float dy = transformedY - lineStartY;
                float radius = sqrt(dx * dx + dy * dy);
                Circle newCircle = {lineStartX, lineStartY, radius, currentPenThickness, {currentColor[0], currentColor[1], currentColor[2]}};
                circles.push_back(newCircle);
            }
            firstClick = true; 
            
            
            glutPostRedisplay();
        }
    }else if (button == GLUT_WHEEL_UP &&glutGetModifiers()==GLUT_ACTIVE_CTRL) { 
            zoomFactor *= 1.1; 
                    
        glMatrixMode(GL_PROJECTION);
        glLoadIdentity();
        gluOrtho2D(-width / 2 * zoomFactor, width / 2 * zoomFactor, -height / 2 * zoomFactor, height / 2 * zoomFactor);
        glMatrixMode(GL_MODELVIEW);

        glutPostRedisplay(); 
        } else if (button == GLUT_WHEEL_DOWN &&glutGetModifiers()==GLUT_ACTIVE_CTRL) { 
            zoomFactor *= 0.9; 
                    
        glMatrixMode(GL_PROJECTION);
        glLoadIdentity();
        gluOrtho2D(-width / 2 * zoomFactor, width / 2 * zoomFactor, -height / 2 * zoomFactor, height / 2 * zoomFactor);
        glMatrixMode(GL_MODELVIEW);

        glutPostRedisplay(); 
        }

    
}
void mouseMove(int x, int y) {
    if (middleButtonPressed) {
        
        offsetX += (x - lastMouseX);
        offsetY -= (y - lastMouseY); 

        
        lastMouseX = x;
        lastMouseY = y;

        
        glutPostRedisplay();
    }
        
}
void menu(int num) {
    switch (num) {
    case 0: 
        exit(0);
        break;
    case 1:
        currentPenThickness = 1;
        break;
    case 2:
        currentPenThickness = 3;
        break;
    case 3:
        currentPenThickness = 5;
        break;
    
    case 4:
        currentColor[0] = 1.0f; currentColor[1] = 0.0f; currentColor[2] = 0.0f; 
        break;
    case 5:
        currentColor[0] = 0.0f; currentColor[1] = 1.0f; currentColor[2] = 0.0f; 
        break;
    
    }

    glutPostRedisplay();
}
void createMenu() {
    int mainmenu, submenu1, submenu2;

    submenu1 = glutCreateMenu(menu);
    glutAddMenuEntry("1 px", 1);
    glutAddMenuEntry("3 px", 2);
    glutAddMenuEntry("5 px", 3);

    submenu2 = glutCreateMenu(menu);
    glutAddMenuEntry("Red", 4);
    glutAddMenuEntry("Green", 5);
    

    mainmenu = glutCreateMenu(menu);
    glutAddSubMenu("Pen Thickness", submenu1);
    glutAddSubMenu("Pen Color", submenu2);
    glutAddMenuEntry("Exit", 0);

    glutAttachMenu(GLUT_RIGHT_BUTTON);
}
int main(int argc, char** argv) {
    glutInit(&argc, argv);
    glutInitDisplayMode(GLUT_DOUBLE | GLUT_RGB);
    glutInitWindowSize(width, height);
    glutCreateWindow("Enhanced Drawing Application");

    init();

    glutDisplayFunc(display);
    glutReshapeFunc(reshape);
    glutKeyboardFunc(keyboard);
    glutMouseFunc(mouse);
    glutMotionFunc(mouseMove); 

    createMenu();

    glutMainLoop();
    return 0;
}
