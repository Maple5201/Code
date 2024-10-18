#include <GL/glut.h>
#include <stdlib.h>
#include <math.h>
#include <vector>
#include <random>
/*  Set initial size of the display window.  */
GLsizei winWidth = 600, winHeight = 600;  

/*  Set size of world-coordinate clipping window.  */
GLfloat xwcMin = -50.0, xwcMax = 50.0;
GLfloat ywcMin = -50.0, ywcMax = 10.0;

bool showLinesAndPoints = true; 
int selectedPointIndex = -1; 
bool dragging = false; 
class wcPt3D {
   public:
      GLfloat x, y, z;
};

// Global control point vector and random number generator
std::vector<wcPt3D> ctrlPts;
std::random_device rd;
std::mt19937 gen(rd());
std::uniform_real_distribution<> dis(-100.0, 100.0); 
GLint nCtrlPts = 0;
void initCtrlPts() {
    nCtrlPts = 6; // Change to desired number of control points
    ctrlPts.resize(nCtrlPts);
    for (int i = 0; i < nCtrlPts; i++) {
        // Use std::uniform_real_distribution to generate floating-point numbers and randomly initialize control points
        ctrlPts[i] = {
            static_cast<GLfloat>(dis(gen)),
            static_cast<GLfloat>(dis(gen)),
            0.0f 
        };
    }
}
// Update clipping window based on control point function
void updateClippingWindow() {
    GLfloat margin = 10.0;
    if (!ctrlPts.empty()) {
        xwcMin = xwcMax = ctrlPts[0].x;
        ywcMin = ywcMax = ctrlPts[0].y;
        for (const auto& pt : ctrlPts) {
            xwcMin = std::min(xwcMin, pt.x);
            xwcMax = std::max(xwcMax, pt.x);
            ywcMin = std::min(ywcMin, pt.y);
            ywcMax = std::max(ywcMax, pt.y);
        }
    }
    xwcMin -= margin; xwcMax += margin;
    ywcMin -= margin; ywcMax += margin;
}

void init (void)
{
   /*  Set color of display window to white.  */
   glClearColor (1.0, 1.0, 1.0, 0.0);
   initCtrlPts(); 
    updateClippingWindow(); 
}

void plotPoint (wcPt3D bezCurvePt)
{
    glBegin (GL_POINTS);
        glVertex2f (bezCurvePt.x, bezCurvePt.y);
    glEnd ( );
}

/*  Compute binomial coefficients C for given value of n.  */
void binomialCoeffs (GLint n, GLint * C)
{
   GLint k, j;

   for (k = 0;  k <= n;  k++) {
      /*  Compute n!/(k!(n - k)!).  */
      C [k] = 1;
      for (j = n;  j >= k + 1;  j--)
        C [k] *= j;
      for (j = n - k;  j >= 2;  j--)
        C [k] /= j;
   }
}

void computeBezPt (GLfloat t, wcPt3D * bezPt, GLint nCtrlPts,
                    wcPt3D * ctrlPts, GLint * C)
{
   GLint k, n = nCtrlPts - 1;
   GLfloat bezBlendFcn;

   bezPt->x = bezPt->y = bezPt->z = 0.0;

   /*  Compute blending functions and blend control points. */
   for (k = 0; k < nCtrlPts; k++) {
      bezBlendFcn = C [k] * pow (t, k) * pow (1 - t, n - k);
      bezPt->x += ctrlPts [k].x * bezBlendFcn;
      bezPt->y += ctrlPts [k].y * bezBlendFcn;
      bezPt->z += ctrlPts [k].z * bezBlendFcn;
   }
}

void bezier (wcPt3D * ctrlPts, GLint nCtrlPts, GLint nBezCurvePts)
{
   wcPt3D bezCurvePt;
   GLfloat t;
   GLint *C;

   /*  Allocate space for binomial coefficients  */
   C = new GLint [nCtrlPts];

   binomialCoeffs (nCtrlPts - 1, C);
   for (int i = 0;  i <= nBezCurvePts;  i++) {
      t = GLfloat (i) / GLfloat (nBezCurvePts);
      computeBezPt (t, &bezCurvePt, nCtrlPts, ctrlPts, C);
      plotPoint (bezCurvePt);
   }
   delete [ ] C;
}

void displayFcn (void)
{
   /*  Set example number of control points and number of
    *  curve positions to be plotted along the Bezier curve.
    */
   GLint nBezCurvePts = 1000;




   glClear (GL_COLOR_BUFFER_BIT);   


    
    if (showLinesAndPoints) {
        // Draw lines connecting control points
        glColor3f(0.7, 0.7, 0.7); 
        glLineWidth(1.0); 
        glBegin(GL_LINE_STRIP); 
        for (const auto& pt : ctrlPts) {
            glVertex2f(pt.x, pt.y);
        }
        glEnd();

        // Draw ctrlPts
        glPointSize(5.0);
        glColor3f(0.0, 0.0, 1.0);
        glBegin(GL_POINTS); 
        for (const auto& pt : ctrlPts) {
            glVertex2f(pt.x, pt.y);
        }
        glEnd();
    }
   bezier (ctrlPts.data(), nCtrlPts, nBezCurvePts);
   glutSwapBuffers();
}

void winReshapeFcn (GLint newWidth, GLint newHeight)
{
winWidth = newWidth; 
    winHeight = newHeight;
   /*  Maintain an aspect ratio of 1.0.  */
   glViewport (0, 0, newWidth, newHeight);

   glMatrixMode (GL_PROJECTION);
   glLoadIdentity ( );

   gluOrtho2D (xwcMin, xwcMax, ywcMin, ywcMax);
updateClippingWindow();
   glutPostRedisplay();
}
void keyboardFcn(unsigned char key, int x, int y) {
    switch (key) {
    case 's':
    case 'S':
        showLinesAndPoints = !showLinesAndPoints; 
        glutPostRedisplay(); 
        break;
    default:
        break;
    }
}
void mouseFunc(int button, int state, int x, int y) {
    if (!showLinesAndPoints) return; 

    // Converts window coordinates to world coordinates
    float wx = (x / (float)winWidth) * (xwcMax - xwcMin) + xwcMin;
    float wy = ((winHeight - y) / (float)winHeight) * (ywcMax - ywcMin) + ywcMin;

    if (button == GLUT_LEFT_BUTTON) {
        if (state == GLUT_DOWN) {
            
            for (int i = 0; i < ctrlPts.size(); i++) {
                float dx = ctrlPts[i].x - wx;
                float dy = ctrlPts[i].y - wy;
                float distance = sqrt(dx * dx + dy * dy);
                if (distance < 5.0) { 
                    selectedPointIndex = i;
                    dragging = true;
                    break;
                }
            }
        } else if (state == GLUT_UP && dragging) {
            dragging = false;
            selectedPointIndex = -1;
        }
    }
}

void motionFunc(int x, int y) {
    if (selectedPointIndex != -1) {
        
        float wx = (x / (float)winWidth) * (xwcMax - xwcMin) + xwcMin;
        float wy = ((winHeight - y) / (float)winHeight) * (ywcMax - ywcMin) + ywcMin;

        
        ctrlPts[selectedPointIndex].x = wx;
        ctrlPts[selectedPointIndex].y = wy;



        glutPostRedisplay(); 
    }
}
int main (int argc, char** argv)
{
   glutInit (&argc, argv);
   glutInitDisplayMode (GLUT_DOUBLE | GLUT_RGB);
   glutInitWindowPosition (50, 50);
   glutInitWindowSize (winWidth, winHeight);
   glutCreateWindow ("Bezier Curve");

   init ( );
   glutDisplayFunc (displayFcn);
   glutReshapeFunc (winReshapeFcn);
   glutKeyboardFunc(keyboardFcn); 
   glutMouseFunc(mouseFunc); 
   glutMotionFunc(motionFunc);
   glutMainLoop ( );
}
